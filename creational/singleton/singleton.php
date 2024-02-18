<?php

class GlobalLogger
{
    private static ?GlobalLogger $instance = null;
    private array $logs = [];

    private function __construct()
    {
    }

    public static function getInstance(): GlobalLogger
    {
        if (self::$instance === null) {
            self::$instance = new GlobalLogger();
        }

        return self::$instance;
    }

    public function log(string $message): void
    {
        $this->logs[] = $message;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}

class SomeAction
{
    public function doSomething()
    {
        $logger = GlobalLogger::getInstance();
        $logger->log("Did something");
    }
}

class AnotherAction
{
    public function doSomethingElse()
    {
        $logger = GlobalLogger::getInstance();
        $logger->log("Did something else");
    }
}

$someAction = new SomeAction();
$someAction->doSomething();
$anotherAction = new AnotherAction();
$anotherAction->doSomethingElse();

$logger = GlobalLogger::getInstance();
var_dump($logger->getLogs());