<?php

abstract class Employee
{
    protected ?Employee $parent;

    public function setManager(?Employee $parent): void
    {
        $this->parent = $parent;
    }

    public function getManager(): Employee
    {
        return $this->parent;
    }

    public function hire(Employee $component): void
    {
    }

    public function fire(Employee $component): void
    {
    }

    public function isManager(): bool
    {
        return false;
    }

    abstract public function info(): string;
}

class SingleEmployee extends Employee
{
    public function __construct(private string $name)
    {
    }

    public function info(): string
    {
        return $this->name;
    }
}

class Manager extends Employee
{
    protected \SplObjectStorage $subordinates;

    public function __construct(private string $name)
    {
        $this->subordinates = new \SplObjectStorage();
    }

    public function hire(Employee $component): void
    {
        $this->subordinates->attach($component);
        $component->setManager($this);
    }

    public function fire(Employee $component): void
    {
        $this->subordinates->detach($component);
        $component->setManager(null);
    }

    public function isManager(): bool
    {
        return true;
    }

    public function info(): string
    {
        $results = [];
        foreach ($this->subordinates as $child) {
            $results[] = $child->info();
        }

        return $this->name . " manages (" . implode(", ", $results) . ")";
    }
}

function structure(Employee $component): void
{
    echo "Struktura: " . $component->info();
}

$ceo = new Manager("Mariusz");

$technology = new Manager("Krzysiek");
$ceo->hire($technology);

$sales = new Manager("Kasia");
$ceo->hire($sales);

$phpDeveloper = new SingleEmployee("Wojtek");
$technology->hire($phpDeveloper);

$reactDeveloper = new SingleEmployee("Kuba");
$technology->hire($reactDeveloper);

$juniorSales = new SingleEmployee("Ania");
$sales->hire($juniorSales);

$design = new SingleEmployee("Janek");

echo "Cała firma:\n";
structure($ceo);
echo "\n\n";

echo "Dział technologii:\n";
structure($technology);
echo "\n\n";

echo "Dział sprzedaży:\n";
structure($sales);
echo "\n\n";

echo "Dział designu:\n";
structure($design);
echo "\n\n";