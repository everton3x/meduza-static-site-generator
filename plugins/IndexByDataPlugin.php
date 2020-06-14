<?php

use Meduza\Build\BuildRepo;
use Meduza\Plugin\PluginInterface;
/**
 * Plugin para criar uma pagina com o conteúdo indexado por data na ordem 
 * decrescente.
 *
 * @author everton
 */
class IndexByDataPlugin implements PluginInterface {
    
    protected BuildRepo $buildRepo;
    
    public function __construct(BuildRepo $buildRepo) {
        $this->buildRepo = $buildRepo;
    }

    public function run(): BuildRepo {
        
        $applyList = $this->buildRepo->get('config.plugins.indexbydate.apply');
//        print_r($applyList);exit();
        
        $index = $this->index();
//        print_r($index); exit();
        
        foreach ($this->buildRepo->get('meta-pages') as $slug => $metaPage){
//            print_r($metaPage);exit();
            if(array_search($metaPage['basename'], $applyList) !== false){
                $metaPage['plugin']['index-by-date'] = $index;
            }
            $metaPages[$slug] = $metaPage;
        }
        
        $this->buildRepo->set('meta-pages', $metaPages);
        
//        print_r($this->buildRepo->get('meta-pages'));
        return $this->buildRepo;
    }
    
    protected function index(): array
    {
        $metaData = $this->buildRepo->get('meta-data');
        $orderByDate = [];

//        print_r($metaData);exit();
        foreach ($metaData as $filePath => $frontmatter) {
            if(key_exists('noindex', $frontmatter)){
                continue;
            }
                        
            $date = date_create_from_format($this->detectFormat($frontmatter['date']), $frontmatter['date']);
            if($date === false){
                throw new Exception("Não foi possível interpretar a data [{$frontmatter['date']}] no arquivo [$filePath].");
            }
            $slug = $metaData[$filePath]['slug'];
            $orderByDate[$slug] = $date->getTimestamp();
        }

        arsort($orderByDate, SORT_NUMERIC);
        
        return $orderByDate;
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
