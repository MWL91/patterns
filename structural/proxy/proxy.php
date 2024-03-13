<?php

interface CurrencyCalculator
{
    public function get(string $currency): float;
}

class FiatCurrencyCalculator implements CurrencyCalculator
{
    public function get(string $currency): float
    {
        echo "Właśnie pobraliśmy PLN w ".strtoupper($currency).".\n";
        return match($currency) {
            'usd' => 3.8,
            'eur' => 4.3,
        };
    }
}

class CryptoCurrencyCalculator implements CurrencyCalculator
{
    public function get(string $currency): float
    {
        echo "Właśnie pobraliśmy PLN w ".strtoupper($currency).".\n";
        return match($currency) {
            'btc' => 200000,
            'eth' => 5000,
        };
    }
}

class CalculateCurrency implements CurrencyCalculator
{
    private array $cache = [];

    public function __construct(private CurrencyCalculator $currencyValue)
    {
    }

    public function get(string $currency): float
    {
        if (!isset($this->cache[$currency])) {
            $result = $this->currencyValue->get($currency);
            $this->cache[$currency] = $result;
        }

        return $this->cache[$currency];
    }
}

function calculate(CurrencyCalculator $currencyValue, string $currency): void
{
    $calculator = new CalculateCurrency($currencyValue);
    echo $calculator->get($currency)."zł \n";
    echo "Teraz już mamy tą wartość, więc nie pobieramy jej ponownie.\n";
    echo $calculator->get($currency)."zł \n";
}

calculate(new FiatCurrencyCalculator(), 'usd');
calculate(new FiatCurrencyCalculator(), 'eur');
echo "\n-----\n";
calculate(new CryptoCurrencyCalculator(), 'btc');
calculate(new CryptoCurrencyCalculator(), 'eth');