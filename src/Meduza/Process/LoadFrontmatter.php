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
        $this->buildRepo->set('meta-data', $frontmatter);

        return $this->buildRepo;
    }
}
