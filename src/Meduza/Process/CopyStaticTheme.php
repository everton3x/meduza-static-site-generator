<?php
namespace Meduza\Process;

/**
 * Copia o conteúdo estático do tema.
 *
 * @author Everton
 */
class CopyStatictheme
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
        
        $staticDir= (new \PTK\FileSystem\Path())->join($this->buildRepo->get('config.theme.dir'), $this->buildRepo->get('config.theme.name'), 'static')->slashes(DIRECTORY_SEPARATOR)->endSlash(DIRECTORY_SEPARATOR)->get();
        $staticDir = (new \PTK\FileSystem\Directory($staticDir))->realpath()->get();
//        echo $target, PHP_EOL;
//        echo $staticDir, PHP_EOL;
        $this->recursiveCopy($staticDir, $target);
        
        
        return $this->buildRepo;
    }
    
    protected function recursiveCopy(string $source, string $target)
    {
        $dir = opendir($source);
        @mkdir($target, 0777, true);
        while (false !== ( $item = readdir($dir))) {
            if (( $item != '.' ) && ( $item != '..' )) {
                $sourceItem = (new \PTK\FileSystem\Path())->join($source, $item);
                $targetItem = (new \PTK\FileSystem\Path())->join($target, $item);
                if (is_dir($sourceItem)) {
                    $this->recursiveCopy($sourceItem, $targetItem);
                } else {
                    copy($sourceItem, $targetItem);
                }
            }
        }
        closedir($dir);
    }
}
