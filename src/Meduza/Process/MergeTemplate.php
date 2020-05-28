<?php
namespace Meduza\Process;

/**
 * Mescla o conteúdo com os templates gerando o html final
 *
 * @author Everton
 */
class MergeTemplate
{
    protected \Meduza\Build\BuildRepo $buildRepo;
    
    protected \LogMan\Messenger\MessengerInterface $logger;

    protected \Twig\Loader\FilesystemLoader $loader;
    
    protected \Twig\Environment $env;

    public function __construct(\Meduza\Build\BuildRepo $buildRepo, \LogMan\Messenger\MessengerInterface $logger)
    {
        $this->buildRepo = $buildRepo;
        $this->logger = $logger;
    }
    
    public function run(): \Meduza\Build\BuildRepo
    {
        $metaPages = $this->buildRepo->get('meta-pages');
        $config = $this->buildRepo->get('config');
        $themeDir= (new \PTK\FileSystem\Path())->join($this->buildRepo->get('config.theme.dir'), $this->buildRepo->get('config.theme.name'))->slashes(DIRECTORY_SEPARATOR)->endSlash(DIRECTORY_SEPARATOR)->get();
        $themeDir = (new \PTK\FileSystem\Directory($themeDir))->realpath()->get();
//        echo $themeDir, PHP_EOL;
        $this->prepareEnvironment($themeDir);
        
        foreach ($metaPages as $slug => $page){
            if(key_exists('template', $page['meta-data'])){
                $template = $page['meta-data']['template'];
            }else{
                $template = $config['defaults']['template'];
            }
            
            $templateFile = "$template.twig";
//            $templateFile = (new \PTK\FileSystem\File(
//                (new \PTK\FileSystem\Path())->join($themeDir, "$template.twig")->slashes(DIRECTORY_SEPARATOR)->get()
//            ))->get(false);
//            echo $templateFile, PHP_EOL;
            $output[$slug] = $this->merge($templateFile, $page, $config);
//            print_r($output);
        }
        
        $this->buildRepo->set('output', $output);
        
        return $this->buildRepo;
    }
    
    protected function merge(string $templateFile, array $page, array $config): string
    {
        return $this->env->render($templateFile, [
            'config' => $config,
            'page' => [
                'config' => $page['meta-data'],
                'content' => $page['html']
            ]
        ]);
    }
    
    protected function prepareEnvironment(string $themeDir)
    {
        $this->loader = new \Twig\Loader\FilesystemLoader($themeDir);
        $this->env = new \Twig\Environment($this->loader, [
            'autoescape' => false //necessário porque se não o twig escapa os caracteres html do conteúdo
        ]);
    }
}
