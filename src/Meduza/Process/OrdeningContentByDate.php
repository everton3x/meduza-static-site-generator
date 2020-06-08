<?php
namespace Meduza\Process;

use Exception;
use LogMan\Messenger\MessengerInterface;
use Meduza\Build\BuildRepo;

/**
 * Ordena uma lista de paginas por data.
 *
 * @author Everton
 */
class OrdeningContentByDate implements ProcessInterface
{

    protected BuildRepo $buildRepo;
    protected MessengerInterface $logger;

    public function __construct(BuildRepo $buildRepo, MessengerInterface $logger)
    {
        $this->buildRepo = $buildRepo;
        $this->logger = $logger;
    }

    public function run(): BuildRepo
    {
        $metaData = $this->buildRepo->get('meta-data');
        $orderByData = [];

        foreach ($metaData as $filePath => $frontmatter) {
                        
            $date = date_create_from_format($this->detectFormat($frontmatter['date']), $frontmatter['date']);
            if($date === false){
                throw new Exception("Não foi possível interpretar a data [{$frontmatter['date']}] no arquivo [$filePath].");
            }
            
            $orderByDate[$filePath] = $date->getTimestamp();
        }

        arsort($orderByData, SORT_NUMERIC);
        
        $this->buildRepo->set('order-by-date', $orderByDate);

        return $this->buildRepo;
    }
    
    /**
     * Detecta o formato da data.
     * 
     * @param string $date
     * @return string
     */
    protected function detectFormat(string $date): string
    {
       if(preg_match('/^(\d)+\-(\d)+\-(\d)+\ (\d)+\:(\d)+\:(\d)+$/', $date)) {
           return 'Y-m-d H:i:s';
       }
       
       if(preg_match('/^(\d)+\-(\d)+\-(\d)+\ (\d)+\:(\d)+$/', $date)) {
           return 'Y-m-d H:i';
       }
       
       if(preg_match('/^(\d)+\-(\d)+\-(\d)+$/', $date)) {
           return 'Y-m-d';
       }
       
       return '';
    }

}
