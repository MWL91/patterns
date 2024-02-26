<?php

class CompanyAddress
{
    private string $id;
    private array $sharedState;

    public function __construct(string $id, array $sharedState)
    {
        $this->id = $id;
        $this->sharedState = $sharedState;
    }

    public function print(array $uniqueState): void
    {
        $s = json_encode($this->sharedState);
        $u = json_encode($uniqueState);
        echo "Stan współdzielony ($s) dla ID ".$this->id." stan unikalny ($u).\n";
    }
}

class FlyweightFactory
{
    /**
     * @var CompanyAddress[]
     */
    private array $flyweights = [];

    public function __construct(array $initialFlyweights)
    {
        foreach ($initialFlyweights as $state) {
            $id = array_shift($state);
            $this->flyweights[$this->getKey($state)] = new CompanyAddress($id, $state);
        }
    }

    private function getKey(array $state): string
    {
        ksort($state);

        return implode("_", $state);
    }

    public function getFlyweight(array $sharedState): CompanyAddress
    {
        $key = $this->getKey($sharedState);

        if (!isset($this->flyweights[$key])) {
            echo "Tworzę nowy stan firmy.\n";
            $this->flyweights[$key] = new CompanyAddress(uniqid(), $sharedState);
        } else {
            echo "Pobieram pobrany wcześniej stan.\n";
        }

        return $this->flyweights[$key];
    }

    public function listFlyweights(): void
    {
        $count = count($this->flyweights);
        echo "\nIlość pyłków w pamięci: $count\n";
        foreach ($this->flyweights as $key => $flyweight) {
            echo $key . "\n";
        }
    }
}

$factory = new FlyweightFactory([
    [uniqid(), '6793108059', 'Inpost sp. z o. o.', 'ul. PANA TADEUSZA 4', '30-727', 'Kraków'],
    [uniqid(), '5321956443', 'Grycan', 'ul. KWITNĄCEJ WIŚNI 2', '05-462', 'Majdan']
]);
$factory->listFlyweights();

// ...

function displayCompanyWorker(
    FlyweightFactory $ff, string $nip, string $company, string $address, string $zip, string $city, string $workerFullName
) {
    // Store company
    $flyweight = $ff->getFlyweight([$nip, $company, $address, $zip, $city]);

    // Display company extended with owner
    $flyweight->print([$workerFullName]);
}

displayCompanyWorker(
    $factory,
    '9562318640',
    'Exulto sp. z o. o.',
    'ul. Włocławska 167',
    '87-100',
    'Toruń',
    'Marcin Lenkowski'
);

displayCompanyWorker(
    $factory,
    '6793108059',
    'Inpost sp. z o. o.',
    'ul. PANA TADEUSZA 4',
    '30-727',
    'Kraków',
    'Rafał Brzoska'
);

displayCompanyWorker(
    $factory,
    '5321956443',
    'Grycan',
    'ul. KWITNĄCEJ WIŚNI 2',
    '05-462',
    'Majdan',
    'Zbigniew Grycan'
);

displayCompanyWorker(
    $factory,
    '9562318640',
    'Exulto sp. z o. o.',
    'ul. Włocławska 167',
    '87-100',
    'Toruń',
    'Marta Lenkowska'
);

$factory->listFlyweights();
