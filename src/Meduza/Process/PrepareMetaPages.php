<?php
namespace Meduza\Process;

/**
 * Prepara meta-páginas.
 * 
 * Meta-páginas nada mais é que uma lista com os dados principais das páginas 
 * que serão utilizadas no processo de renderização do conteúdo final.
 * 
 * Também é onde os plugins podem criar outras páginas, como páginas de tags, 
 * categorias, etc.
 *
 * @author Everton
 */
class PrepareMetaPages implements ProcessInterface
{

    protected \Meduza\Build\BuildRepo $buildRepo;
    protected \LogMan\Messenger\MessengerInterface $logger;

    public function __construct(\Meduza\Build\BuildRepo $buildRepo, \LogMan\Messenger\MessengerInterface $logger)
    {
        $this->buildRepo = $buildRepo;
        $this->logger = $logger;
    }

    public function run(): \Meduza\Build\BuildRepo
    {
        $filesList = $this->buildRepo->get('content');
        $metaDataList = $this->buildRepo->get('meta-data');
        $parsableList = $this->buildRepo->get('parsable');
        $metaPages = [];

        foreach ($filesList as $filePath => $fileObj) {
            $this->logger->debug("Preparando meta-página de {file}", ['file' => $filePath]);
            
            if(!key_exists($filePath, $metaDataList)){
                throw new \Exception("Não foi encontrado meta-dados para [$filePath]");
            }
            $metaPages[$filePath]['meta-data'] = $metaDataList[$filePath];
            
            if(!key_exists($filePath, $parsableList)){
                throw new \Exception("Não foi encontrado conteúdo para [$filePath]");
            }
            $metaPages[$filePath]['parsable'] = $parsableList[$filePath];
            
            $metaPages[$filePath]['extension'] = $fileObj->getExtension();
            $metaPages[$filePath]['basename'] = $fileObj->getBasename(".{$fileObj->getExtension()}");
            $metaPages[$filePath]['dirPath'] = (new \PTK\FileSystem\Path($fileObj->getPath()))->endSlash()->get();
            
            
        }
        $this->buildRepo->set('meta-pages', $metaPages);

        return $this->buildRepo;
    }
}
