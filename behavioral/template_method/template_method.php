<?php

abstract class Storage
{
    protected array $data = [];

    abstract protected function build(): string;

    abstract protected function getFilename(): string;

    public function store(): void
    {
        $this->filter();
        $preparedData = $this->build();
        $compressed = $this->compress($preparedData);
        $this->write($compressed);
    }

    public function addToStorage(?string $payload): void
    {
        $this->data[] = $payload;
    }

    protected function filter(): void
    {
        $this->data = array_filter($this->data, fn($item) => $item !== null);
    }

    protected function compress(string $preparedData): string
    {
        return gzcompress($preparedData);
    }

    protected function write(string $compressed): void
    {
        file_put_contents($this->getFilename(), $compressed);
    }

}

final class JsonStorage extends Storage
{
    protected function build(): string
    {
        return json_encode($this->data);
    }

    protected function getFilename(): string
    {
        return 'data.json';
    }
}

final class CsvStorage extends Storage
{
    public function __construct(private string $delimiter = ',')
    {
    }

    protected function build(): string
    {
        return implode($this->delimiter, $this->data);
    }

    protected function getFilename(): string
    {
        return 'data.csv';
    }
}

final class XmlStorage extends Storage
{
    protected function build(): string
    {
        $xml = new SimpleXMLElement('<root/>');
        foreach ($this->data as $item) {
            $xml->addChild('item', $item);
        }
        return $xml->asXML();
    }

    protected function getFilename(): string
    {
        return 'data.xml';
    }

    protected function compress(string $preparedData): string
    {
        return $preparedData;
    }
}

function clientCode(Storage $storage): void
{
    $storage->addToStorage('some data');
    $storage->addToStorage(null);
    $storage->addToStorage('something else');
    $storage->store();
}

clientCode(new JsonStorage());
clientCode(new CsvStorage(';'));
clientCode(new XmlStorage());