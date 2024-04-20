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
    public function __construct(
        private array $products,
        private Strategy $strategy
    )
    {
        if(count(array_filter($this->products, fn($product) => !($product instanceof Product)))) {
            throw new InvalidArgumentException('Products must be instance of Product');
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
}

class StandardCustomerStrategy implements Strategy
{
    public function getPrice(array $products): float
    {
        return array_sum(array_map(fn($product) => $product->getPrice(), $products));
    }
}

class BuyTwoPayOneStrategy implements Strategy
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
}

$products = [
    new Product('AAA', 10),
    new Product('AAA', 10),
    new Product('BBB', 30),
    new Product('AAA', 10)
];

$standardCustomerCart = new Cart($products, new StandardCustomerStrategy());
echo 'Standard price: ' . $standardCustomerCart->calculatePrice() . "zł\n";

$standardCustomerCart = new Cart($products, new WeekendBuyTwoPayOneStrategy());
echo 'Discount price: ' . $standardCustomerCart->calculatePrice() . "zł\n";