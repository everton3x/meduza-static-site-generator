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

            if (!key_exists($filePath, $metaDataList)) {
                throw new \Exception("Não foi encontrado meta-dados para [$filePath]");
            }

            $slug = $this->getSlug($metaDataList[$filePath], $filePath);
            $metaPages[$slug]['slug'] = $slug;
            $metaDataList[$filePath]['slug'] = $slug;
            $metaPages[$slug]['meta-data'] = $metaDataList[$filePath];

            if (!key_exists($filePath, $parsableList)) {
                throw new \Exception("Não foi encontrado conteúdo para [$filePath]");
            }
            $metaPages[$slug]['parsable'] = $parsableList[$filePath];

            $metaPages[$slug]['extension'] = $fileObj->getExtension();
            $metaPages[$slug]['basename'] = $fileObj->getBasename(".{$fileObj->getExtension()}");
            $metaPages[$slug]['dirPath'] = (new \PTK\FileSystem\Path($fileObj->getPath()))->endSlash()->get();
        }
        $this->buildRepo->set('meta-pages', $metaPages);
        $this->buildRepo->set('meta-data', $metaDataList);

        return $this->buildRepo;
    }

    protected function getSlug(array $metaData, $filePath): string
    {
        $siteUrl = (new \PTK\FileSystem\Path($this->buildRepo->get('config.site.url')))->endSlash('/')->get();
        
        if (key_exists('slug', $metaData)) {
            $slug = $metaData['slug'];
            if (substr($slug, -1, 1) === '/') {
                $slug = substr($slug, 0, strlen($slug) - 1);
            }
            if(substr($slug, 0, 1) === '/'){
                $slug = substr($slug, 1);
            }
        }else{
            $dir = (new \PTK\FileSystem\Path(dirname($filePath)))->endSlash()->get();
            $basename = basename($filePath, ".".pathinfo($filePath, PATHINFO_EXTENSION));
            $contentDir = (new \PTK\FileSystem\Directory($this->buildRepo->get('config.content.source')))->realpath()->get();
            $slug = str_replace($contentDir, $siteUrl, "$dir$basename");
        }
        
        
        return "{$slug}";
    }
}
