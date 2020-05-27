<?php
namespace Meduza\Process;

/**
 * Interface para os processos de build
 * @author Everton
 */
interface ProcessInterface
{
    /**
     * Todo processo deve receber uma instância de \Meduza\Build\BuildRepo para 
     * modificá-la ou fazer alguma coisa a partir dos dados armazenados nela.
     * 
     * @param \Meduza\Build\BuildRepo $buildRepo
     * @param \LogMan\Messenger\MessengerInterface $logger Um logger para o processo
     */
    public function __construct(\Meduza\Build\BuildRepo $buildRepo, \LogMan\Messenger\MessengerInterface $logger);
    
    /**
     * Realiza o processamento sobre \Meduza\Build\BuildRepo.
     * 
     * Sempre deve devolver a instância de \Meduza\Build\BuildRepo, modificada
     * ou não, que será a entrada para o processo seguinte.
     * 
     * @return \Meduza\Build\BuildRepo
     */
    public function run(): \Meduza\Build\BuildRepo;
}
