<?php
namespace Meduza\Process;

/**
 * Converte o conteúdo de markdown, ReST, etc, para HTML
 *
 * @author Everton
 */
class ParseToHTML
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
        $parsers = $this->buildRepo->get('config.output.parsers');
//        print_r($parsers);
        $metaPages = $this->buildRepo->get('meta-pages');
//        print_r($metaPages);
        foreach ($metaPages as $slug => $page){
            $extension = $page['extension'];
            $parsable = $page['parsable'];
            if(key_exists($extension, $parsers)){
                $parserClass = "\\Meduza\\Parser\\{$parsers[$extension]}";
                $parser = new $parserClass();
                $html = $parser->parse($parsable);
            }
            $this->buildRepo->set("meta-pages.{$slug}.html", $html);
//            print_r($html);
//            print_r($this->buildRepo->get("meta-pages.{$slug}.html"));
        }
        
        
        return $this->buildRepo;
    }
    
    
}
