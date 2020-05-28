<?php
namespace Meduza\Process;

/**
 * Salva o conteúdo mesclado no disco.
 *
 * @author Everton
 */
class SaveOutput
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
        $target = (new \PTK\FileSystem\Directory(
            (new \PTK\FileSystem\Path($this->buildRepo->get('config.output.target')))
                ->slashes(DIRECTORY_SEPARATOR)
                ->endSlash(DIRECTORY_SEPARATOR)
            ))
        ->realpath()
        ->get();
//        echo $outputDir, PHP_EOL;
        
        $this->clearTarget($target);
        
        
        return $this->buildRepo;
    }
    
    protected function clearTarget(string $target)
    {
        (new \PTK\FileSystem\Directory($target))->remove();
        
    }
}
