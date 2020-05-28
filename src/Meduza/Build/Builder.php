<?php
namespace Meduza\Build;

/**
 * Construtor do projeto.
 * 
 * É o maestro do processo de construção do conteúdo estático.
 * 
 * Sua responsabilidade é conduzir o processo de construção.
 *
 * @author everton
 */
class Builder
{

    public function __construct()
    {
        
    }

    public function build(array $config, \LogMan\Messenger\MessengerInterface $logger)
    {
        $logger->info("Construção iniciada em {now}", [
            'now' => date('Y-m-d H:i:s')
        ]);
        
        $logger->notice("Iniciando repositório de construção...");
        $buildRepo = new BuildRepo();
        
        //<configurações>
        $logger->notice("Salvando configurações no repositório de construção...");
        $buildRepo->set('config', $config);
        //</configurações>
        
        //<lendo conteúdo>
        $logger->notice("Carregando conteúdo de {contentDir}", [
            'contentDir' => $config['content']['source']
        ]);
        $processLoadContent = new \Meduza\Process\LoadContent($buildRepo, $logger);
        $buildRepo = $processLoadContent->run();
        $logger->debug('Foram encontrados em [{content}] {files} arquivos.', [
            'content' => $buildRepo->get('config.content.source'),
            'files' => count($buildRepo->get('content'))
        ]);
        //</lendo conteúdo>
        
        //<frontmatter>
        $logger->notice("Carregando meta-dados dos arquivos de conteúdo...");
        $processLoadFrontmatter = new \Meduza\Process\LoadFrontmatter($buildRepo, $logger);
        $buildRepo = $processLoadFrontmatter->run();
        $logger->debug('Foram encontrados meta-dados em {files} arquivos.', [
            'files' => count($buildRepo->get('meta-data'))
        ]);
//        print_r($buildRepo->get('meta-data'));
        //</frontmatter>
        
        //<content>
        $logger->notice("Carregando conteúdo dos arquivos de conteúdo...");
        $processLoadParsableContent = new \Meduza\Process\LoadParsableContent($buildRepo, $logger);
        $buildRepo = $processLoadParsableContent->run();
        $logger->debug('Foram encontrados conteúdo em {files} arquivos.', [
            'files' => count($buildRepo->get('parsable'))
        ]);
//        print_r($buildRepo->get('parsable'));
        //</content>
        
        //<constroi meta-paginas>
        $logger->notice("Preparando meta-páginas...");
        $processPrepareMetaPages = new \Meduza\Process\PrepareMetaPages($buildRepo, $logger);
        $buildRepo = $processPrepareMetaPages->run();
        $logger->debug('Foram preparadas {meta-pages} meta-páginas.', [
            'meta-pages' => count($buildRepo->get('meta-pages'))
        ]);
//        foreach ($buildRepo->get('meta-pages') as $key => $metapage){
//            echo $metapage['slug'], PHP_EOL;
//        }
//        print_r($buildRepo);
        //</constroi meta-paginas>
        
        //<plugins>
        //</plugins>
        
        //<parse conteúdo>
        $logger->notice("Traduzindo conteúdo para HTML...");
        $processParseToHTML = new \Meduza\Process\ParseToHTML($buildRepo, $logger);
        $buildRepo = $processParseToHTML->run();
        $logger->debug('Foram traduzidas {meta-pages} meta-páginas.', [
            'meta-pages' => count($buildRepo->get('meta-pages'))
        ]);
//        print_r($buildRepo);
        //</parse conteúdo>
        
        //<merge temas>
        //</merge temas>
        
        //<salva output>
        //</salva output>
        
        //<copia conteúdo estático>
        //</copia conteúdo estático>
        
        ////<copia conteúdo estático do tema>
        //</copia conteúdo estático do tema>
    }
}
