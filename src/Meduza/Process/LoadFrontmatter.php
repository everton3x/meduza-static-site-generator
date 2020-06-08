<?php
namespace Meduza\Process;

/**
 * Carrega os meta-dados (front matter) dos arquivos de conteúdo
 *
 * @author Everton
 */
class LoadFrontmatter implements ProcessInterface
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
            $this->logger->debug("Lendo frontmatter de {file}", ['file' => $filePath]);
            if (($frontmatter[$filePath] = yaml_parse_file($filePath, 0)) === false) {
                throw new \Exception("Não foi possível ler os meta-dados de [$filePath]");
            }
        }

        $this->injectDateOfCreation($frontmatter);

        $this->injectDateOfModification($frontmatter);

        print_r($frontmatter);
        exit();

        $this->buildRepo->set('meta-data', $frontmatter);

        return $this->buildRepo;
    }

    /**
     * Injeta a data de criação se não estiver disponível no frontmatter
     * 
     * @param array $frontmatter
     */
    protected function injectDateOfCreation(array &$frontmatter)
    {
        foreach ($frontmatter as $filePath => $metaData) {
            if (key_exists('date', $metaData) === false) {
                if (key_exists('modification', $metaData)) {
                    $frontmatter[$filePath]['date'] = $metaData['modification'];
                } else {
                    $frontmatter[$filePath]['date'] = date('Y-m-d H:i:s', filemtime($filePath));
                }
            }
        }
    }

    /**
     * Injeta a data de modificação se não estiver disponível no frontmatter
     * 
     * @param array $frontmatter
     */
    protected function injectDateOfModification(array &$frontmatter)
    {
        foreach ($frontmatter as $filePath => $metaData) {
            if (key_exists('modification', $metaData) === false) {
                $frontmatter[$filePath]['modification'] = date('Y-m-d H:i:s', filemtime($filePath));
            }
        }
    }
}
