<?php

interface Input
{
    public function read(): array;
}

interface Store
{
    public function store(array $data): void;
}

abstract class Converter
{
    abstract protected function inputMethod(): Input;
    abstract protected function storeMethod(): Store;

    public function convert(): void
    {
        $data = $this->inputMethod()->read();
        $this->storeMethod()->store($data);
    }
}

final class GamesToExcel extends Converter
{
    protected function inputMethod(): Input
    {
        return new XboxGamesApi();
    }

    protected function storeMethod(): Store
    {
        return new CsvStore('games.csv');
    }
}

final class GamesToMyApp extends Converter
{
    protected function inputMethod(): Input
    {
        return new XboxGamesApi();
    }

    protected function storeMethod(): Store
    {
        return new MyServerApi();
    }
}

class XboxGamesApi implements Input
{
    public function read(): array
    {
        return array_map(
            fn(array $array) => [
                $array["id"],
                $array["name"],
                $array["genre"][0] ?? '',
                $array["publishers"][0] ?? '',
            ],
            json_decode($this->fetchData(), true)
        );
    }

    private function fetchData(): string
    {
        return @file_get_contents('https://api.sampleapis.com/xbox/games');
    }
}

class CsvStore implements Store
{
    public function __construct(private string $source)
    {
    }

    public function store(array $data): void
    {
        $fp = fopen($this->source, 'w');

        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }
}

class MyServerApi implements Input, Store
{
    public function read(): array
    {
        return json_decode($this->fetchData(), true) ?? [];
    }

    private function fetchData(): string
    {
        return @file_get_contents($this->getBaseUrl() . '/games');
    }

    private function getBaseUrl(): string
    {
        return 'https://localhost/api';
    }

    public function store(array $data): void
    {
        $ch = curl_init($this->getBaseUrl() . '/create');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_exec($ch);

        curl_close($ch);
    }
}

function convert(Converter $converter)
{
    $converter->convert();
}

// This will store games in excel
convert(new GamesToExcel());

// This will store games in my app
convert(new GamesToMyApp());

// This will store data from my server in csv file
convert(new class extends Converter {
    protected function inputMethod(): Input
    {
        return new MyServerApi();
    }

    protected function storeMethod(): Store
    {
        return new CsvStore('my_games.csv');
    }
});