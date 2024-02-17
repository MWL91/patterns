<?php

enum EngineType {
    case AUTOMATIC;
    case MANUAL;
}

enum Accessory {
    case GPS;
    case TRAILER;
    case RADIO;
}

interface Builder
{
    public function getOffer(): string;
    public function getPrice(): float;

    public function setEngine(EngineType $type): void;

    public function setMileage(int $mileage): void;

    public function addAccessories(Accessory $accessory): void;
}

class Jeep implements Builder
{
    private EngineType $engineType;
    private int $mileage;
    private array $accessories = [];

    public function setEngine(EngineType $type): void
    {
        $this->engineType = $type;
    }

    public function setMileage(int $mileage): void
    {
        $this->mileage = $mileage;
    }

    public function addAccessories(Accessory $accessory): void
    {
        $this->accessories[] = $accessory;
    }

    public function getOffer(): string
    {
        $accessories = join(', ', array_map(fn($accessory) => $accessory->name, $this->accessories));
        return "Jeep with {$this->engineType->name} engine, {$this->mileage} mileage and accessories: ".($accessories === '' ? '-' : $accessories);
    }

    public function getPrice(): float
    {
        switch($this->mileage) {
            case $this->mileage > 100000:
                $uses = 0.8;
                break;
            case $this->mileage > 200000:
                $uses = 0.7;
                break;
            default:
                $uses = 1;
        }

        return (match($this->engineType) {
            EngineType::AUTOMATIC => 30000,
            EngineType::MANUAL => 25000
        } + count($this->accessories) * 1000) * $uses;
    }
}

class Ford implements Builder
{
    private EngineType $engineType;
    private int $mileage;
    private array $accessories = [];

    public function setEngine(EngineType $type): void
    {
        $this->engineType = $type;
    }

    public function setMileage(int $mileage): void
    {
        $this->mileage = $mileage;
    }

    public function addAccessories(Accessory $accessory): void
    {
        $this->accessories[] = $accessory;
    }

    public function getOffer(): string
    {
        $accessories = join(', ', array_map(fn($accessory) => $accessory->name, $this->accessories));
        return "Ford with {$this->engineType->name} engine, {$this->mileage} mileage and accessories: ".($accessories === '' ? '-' : $accessories);
    }

    public function getPrice(): float
    {
        switch($this->mileage) {
            case $this->mileage > 100000:
                $uses = 0.7;
                break;
            case $this->mileage > 200000:
                $uses = 0.6;
                break;
            default:
                $uses = 1;
        }

        return (match($this->engineType) {
                    EngineType::AUTOMATIC => 20000,
                    EngineType::MANUAL => 15000
                } + count($this->accessories) * 1200) * $uses;
    }
}

class Bmw implements Builder
{
    private EngineType $engineType;
    private int $mileage;
    private array $accessories = [];

    public function setEngine(EngineType $type): void
    {
        $this->engineType = $type;
    }

    public function setMileage(int $mileage): void
    {
        $this->mileage = $mileage;
    }

    public function addAccessories(Accessory $accessory): void
    {
        $this->accessories[] = $accessory;
    }

    public function getOffer(): string
    {
        $accessories = join(', ', array_map(fn($accessory) => $accessory->name, $this->accessories));
        return "BMW with {$this->engineType->name} engine, {$this->mileage} mileage and accessories: ".($accessories === '' ? '-' : $accessories);
    }

    public function getPrice(): float
    {
        switch($this->mileage) {
            case $this->mileage > 100000:
                $uses = 0.8;
                break;
            case $this->mileage > 200000:
                $uses = 0.7;
                break;
            default:
                $uses = 1;
        }

        return (match($this->engineType) {
                    EngineType::AUTOMATIC => 50000,
                    EngineType::MANUAL => 45000
                } + count($this->accessories) * 2000) * $uses;
    }
}

function createCarOffer(Builder $builder)
{
    $builderInit = clone $builder;

    echo "Standard basic product:\n";
    $builder->setEngine(EngineType::MANUAL);
    $builder->setMileage(50000);
    echo $builder->getOffer();
    echo "\nPrice: ".$builder->getPrice()."$\n";

    echo "\n\nCustom product:\n";
    $builder = clone $builderInit;
    $builder->setEngine(EngineType::AUTOMATIC);
    $builder->setMileage(0);
    echo $builder->getOffer();
    echo "\nPrice: ".$builder->getPrice()."$\n";

    echo "\n\nStandard full featured product:\n";
    $builder = clone $builderInit;
    $builder->setEngine(EngineType::AUTOMATIC);
    $builder->setMileage(0);
    $builder->addAccessories(Accessory::GPS);
    $builder->addAccessories(Accessory::RADIO);
    $builder->addAccessories(Accessory::TRAILER);
    echo $builder->getOffer();
    echo "\nPrice: ".$builder->getPrice()."$\n";
}

createCarOffer(new Jeep());
createCarOffer(new Ford());
createCarOffer(new Bmw());