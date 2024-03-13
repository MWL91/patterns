<?php

final class Pokemon
{
    public function __construct(
        private string $name,
        private int $weight,
        private int $height,
        private array $abilities
    )
    {
    }

    public function __toString(): string
    {
        return "Name: $this->name, Weight: $this->weight, Height: $this->height, Abilities: " . implode(', ', $this->abilities);
    }
}

class PokemonIterator implements \Iterator
{
    private int $position = 0;
    private array $pokemonList = [];
    private array $pokemonCache = [];

    public function __construct(private int $limit, private int $page = 1)
    {
        $json = file_get_contents('https://pokeapi.co/api/v2/pokemon?limit=' . $limit . '&offset=' . ($page - 1) * $limit);
        $data = json_decode($json, true);
        $this->pokemonList = $data['results'];

        $this->position = ($this->page - 1) * $this->limit;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): Pokemon
    {
        if(!isset($this->pokemonCache[$this->position])) {
            $data = json_decode(file_get_contents($this->pokemonList[$this->position]['url']), true);
            $abilities = array_map(fn(array $ability) => $ability['ability']['name'], $data['abilities']);
            $this->pokemonCache[$this->position] = new Pokemon($this->pokemonList[$this->position]['name'], $data['weight'], $data['height'], $abilities);
        }

        return $this->pokemonCache[$this->position];
    }

    public function key(): string
    {
        return $this->pokemonList[$this->position]['name'];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function valid(): bool
    {
        return $this->position < count($this->pokemonList);
    }
}

$pokemons = new PokemonIterator(3, 1);

foreach ($pokemons as $name => $pokemon) {
    echo "Pokemon: $name" . PHP_EOL;
    echo $pokemon . PHP_EOL;
    echo '---------' . PHP_EOL;
}