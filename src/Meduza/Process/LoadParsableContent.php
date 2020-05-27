<?php
namespace Meduza\Process;

/**
 * Carrega os o conteúdo parsable dos arquivos de conteúdo
 *
 * @author Everton
 */
class LoadParsableContent implements ProcessInterface
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
        $contentFilesList = $this->buildRepo->get('content');

        foreach ($contentFilesList as $filePath => $fileObj) {
            $this->logger->debug("Lendo conteúdo de {file}", ['file' => $filePath]);
            $fileHandle = (new \PTK\FileSystem\File())->open($filePath, \PTK\FileSystem\File::MODE_R);
            $parsable[$filePath] = $this->getParsable($fileHandle);
            $fileHandle->close();
        }
        $this->buildRepo->set('parsable', $parsable);

        return $this->buildRepo;
    }
    
    protected function getParsable($handle): string
    {
        $control = 0;
        $parsable = '';
        while(($line = $handle->getLine(false)) != false){
            if($control >= 2){
                $parsable .= $line;
            }
            if(trim($line) === '---'){
                $control++;
            }
        }
        
        return $parsable;
    }
}
