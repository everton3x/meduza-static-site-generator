<?php
/**
 * Arquivo de construção do conteúdo.
 * 
 * Por enquanto é apenas para teste.
 */

require_once 'bootstrap.php';

$env = Meduza\Environment\Environment::DEVELOPMENT;
//$env = Meduza\Environment::PRODUTION;

$configLoader = new Meduza\Config\Loader();

$config = $configLoader->load('meduza.yml', $env);

$logger = new LogMan\LogMan();
//$logger->toggleDebug(true);
$stdoutLogger = new LogMan\Logger\StdOutLogger();
$stdoutLogger->setLoggerName('Terminal');
$logger->assignLoggerToEmergencyLevel($stdoutLogger)
    ->assignLoggerToAlertLevel($stdoutLogger)
    ->assignLoggerToCriticalLevel($stdoutLogger)
    ->assignLoggerToErrorLevel($stdoutLogger)
    ->assignLoggerToWarningLevel($stdoutLogger)
    ->assignLoggerToNoticeLevel($stdoutLogger)
    ->assignLoggerToInfoLevel($stdoutLogger)
    ->assignLoggerToDebugLevel($stdoutLogger);
$logMessenger = $logger->getMessenger();

$builder = new Meduza\Build\Builder();

$builder->build($config, $logMessenger);