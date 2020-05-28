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
        
        foreach ($this->buildRepo->get('meta-pages') as $slug => $page){
//            print_r($page);continue;
            $fileOutput = str_replace(['//', '\\\\'], DIRECTORY_SEPARATOR, str_replace($this->buildRepo->get('config.site.url'), $target, $slug)).'.html';
            $this->logger->debug("Salvando em $fileOutput");
            
            @mkdir(dirname($fileOutput), 0777, true);
            file_put_contents($fileOutput, $this->buildRepo->get('output')[$slug]);
        }
        
        return $this->buildRepo;
    }
    
    protected function clearTarget(string $target)
    {
//        echo $target, PHP_EOL;exit();
        (new \PTK\FileSystem\Directory($target))->remove();
        mkdir($target, 0777, true);
        
    }
}
