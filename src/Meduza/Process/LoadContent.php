<?php
namespace Meduza\Process;

/**
 * Carrega o conteúdo para ser parseado
 *
 * @author Everton
 */
class LoadContent
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
        $contentDirPath = (new \PTK\FileSystem\Path($this->buildRepo->get('config.content.source')))->slashes()->endSlash()->get();
        
        $this->logger->debug('Lendo conteúdo de {path}', ['path' => $contentDirPath]);
        
        $contentDir = new \PTK\FileSystem\Directory($contentDirPath);
        $this->buildRepo->set('content', $contentDir->realpath()->read(true, \PTK\FileSystem\Directory::ONLY_FILES));
        
        return $this->buildRepo;
    }
}
