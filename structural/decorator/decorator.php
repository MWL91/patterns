<?php

interface Tax
{
    public function calculate(float $price): float;

    public function getName(): string;
}

class Vat implements Tax
{
    public function calculate(float $price): float
    {
        return $price * 1.23;
    }

    public function getName(): string
    {
        return "VAT";
    }
}

abstract class TaxDecorator implements Tax
{
    protected Tax $tax;

    public function __construct(Tax $tax)
    {
        $this->tax = $tax;
    }

    public function calculate(float $price): float
    {
        return $this->tax->calculate($price);
    }

    abstract public function getName(): string;
}

class CitTax extends TaxDecorator
{
    public function __construct(Tax $tax, private float $shareCapital, private float $costs = 0)
    {
        parent::__construct($tax);
    }

    public function calculate(float $price): float
    {
        $price = parent::calculate($price);
        $taxToBeCalculated = $price - $this->shareCapital - $this->costs;
        if($taxToBeCalculated <= 0) return $price;

        return $price + ($taxToBeCalculated * 0.19);
    }

    public function getName(): string
    {
        return "CIT";
    }
}

class IncomeTax extends TaxDecorator
{
    public function calculate(float $price): float
    {
        if(parent::calculate($price) >= 120000) {
            return parent::calculate($price) * 1.32;
        }

        return parent::calculate($price) * 1.12;
    }

    public function getName(): string
    {
        return "INCOME";
    }
}

class IncomeTaxForCopyrightTransfer extends TaxDecorator
{
    public function calculate(float $price): float
    {
        $price = parent::calculate($price);
        return $price + (($price * 0.5) * 0.12);
    }

    public function getName(): string
    {
        return "INCOME";
    }
}

function calculate(Tax $tax, int $price)
{
    echo $tax->getName() . " Tax: " . $tax->calculate($price) . "zł\n";
}

$tax = new Vat();
$cit = new CitTax($tax, 5000, 1000);

// Price with standard income tax
$income = new IncomeTax($cit);
calculate($income, 200000);

// Price with tax for copyright transfer
$income = new IncomeTaxForCopyrightTransfer($cit);
calculate($income, 200000);