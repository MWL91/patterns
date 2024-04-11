<?php

interface DoClass
{
    public function exec(): void;
}

interface GetClass
{
    public function search(): array;
}

interface AbstractFactory
{
    public function do(): DoClass;

    public function get(): GetClass;
}

class Application
{
    public function __construct(private AbstractFactory $factory)
    {
    }

    public function do(): void
    {
        $this->factory->do()->exec();
    }

    public function get(): array
    {
        return $this->factory->get()->search();
    }
}