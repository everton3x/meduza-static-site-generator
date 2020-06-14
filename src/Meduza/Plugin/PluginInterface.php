<?php
namespace Meduza\Plugin;

use Meduza\Build\BuildRepo;

/**
 * Interface para plugins do meduza.
 * @author everton
 */
interface PluginInterface
{
    public function __construct(BuildRepo $buildRepo);
    
    public function run(): BuildRepo;
}
