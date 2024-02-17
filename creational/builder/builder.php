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

interface CarAttributes
{
    public function setEngine(EngineType $type): void;

    public function setMileage(int $mileage): void;

    public function addAccessories(Accessory $accessory): void;
}

interface Car extends CarAttributes
{
    public function getOffer(): string;
    public function getPrice(): float;
}

interface Builder extends CarAttributes
{
    public function getProduct(): Car;

    public function reset(): void;
}

class CarBuilder implements Builder
{
    private Car $product;
    private Car $initialProduct;

    public function __construct(Car $product)
    {
        $this->initialProduct = clone $product;
        $this->reset();
    }

    public function reset(): void
    {
        $this->product = $this->initialProduct;
    }

    public function setEngine(EngineType $type): void
    {
        $this->product->setEngine($type);
    }

    public function setMileage(int $mileage): void
    {
        $this->product->setMileage($mileage);
    }

    public function addAccessories(Accessory $accessory): void
    {
        $this->product->addAccessories($accessory);
    }

    public function getProduct(): Car
    {
        return $this->product;
    }
}

class Jeep implements Car
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

class Ford implements Car
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

class Bmw implements Car
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
    echo "Standard basic product:\n";
    $builder->setEngine(EngineType::MANUAL);
    $builder->setMileage(50000);
    echo $builder->getProduct()->getOffer();
    echo "\nPrice: ".$builder->getProduct()->getPrice()."$\n";

    echo "\n\nCustom product:\n";
    $builder->reset();
    $builder->setEngine(EngineType::AUTOMATIC);
    $builder->setMileage(0);
    echo $builder->getProduct()->getOffer();
    echo "\nPrice: ".$builder->getProduct()->getPrice()."$\n";

    echo "\n\nStandard full featured product:\n";
    $builder->reset();
    $builder->setEngine(EngineType::AUTOMATIC);
    $builder->setMileage(0);
    $builder->addAccessories(Accessory::GPS);
    $builder->addAccessories(Accessory::RADIO);
    $builder->addAccessories(Accessory::TRAILER);
    echo $builder->getProduct()->getOffer();
    echo "\nPrice: ".$builder->getProduct()->getPrice()."$\n";
}

createCarOffer(new CarBuilder(new Jeep()));
createCarOffer(new CarBuilder(new Ford()));
createCarOffer(new CarBuilder(new Bmw()));