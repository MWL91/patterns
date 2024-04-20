<?php

class Product
{
    public function __construct(
        private string $sku,
        private float $price
    )
    {
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}

class Cart
{
    private Strategy $strategy;

    const array STRATEGIES = [
        WeekendBuyTwoPayOneStrategy::class,
        StandardCustomerStrategy::class,
    ];

    public function __construct(
        private array $products
    )
    {
        if(count(array_filter($this->products, fn($product) => !($product instanceof Product)))) {
            throw new InvalidArgumentException('Products must be instance of Product');
        }

        foreach(self::STRATEGIES as $strategy) {
            $strategyInstance = new $strategy();
            if($strategyInstance->isSatisfiedBy()) {
                $this->strategy = $strategyInstance;
                break;
            }
        }
    }

    public function calculatePrice(): float
    {
        return $this->strategy->getPrice($this->products);
    }
}

interface Strategy
{
    public function getPrice(array $products): float;

    public function isSatisfiedBy(): bool;
}

class StandardCustomerStrategy implements Strategy
{
    public function getPrice(array $products): float
    {
        return array_sum(array_map(fn($product) => $product->getPrice(), $products));
    }

    public function isSatisfiedBy(): bool
    {
        return true;
    }
}

class WeekendBuyTwoPayOneStrategy implements Strategy
{
    public function getPrice(array $products): float
    {
        $map = [];
        $count = [];
        $price = 0;

        foreach($products as $product) {
            $map[$product->getSku()] = $product;
            $count[$product->getSku()] = ($count[$product->getSku()] ?? 0) + 1;
        }

        foreach($count as $sku => $amount) {
            $price += ceil($amount / 2) * ($map[$sku]->getPrice());
        }

        return $price;
    }

    public function isSatisfiedBy(): bool
    {
        return date('w') > 5;
    }
}

$products = [
    new Product('AAA', 10),
    new Product('AAA', 10),
    new Product('BBB', 30),
    new Product('AAA', 10)
];

$standardCustomerCart = new Cart($products);
echo 'Price: ' . $standardCustomerCart->calculatePrice() . "zł\n";
