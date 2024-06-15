<?php

interface Specification
{
    public function isSatisfiedBy($candidate): bool;
    public function and(Specification $other): Specification;
    public function or(Specification $other): Specification;
    public function not(): Specification;
}

abstract class AbstractSpecification implements Specification
{
    public function and(Specification $other): Specification
    {
        return new AndSpecification($this, $other);
    }

    public function or(Specification $other): Specification
    {
        return new OrSpecification($this, $other);
    }

    public function not(): Specification
    {
        return new NotSpecification($this);
    }
}

class AndSpecification extends AbstractSpecification
{
    private $one;
    private $other;

    public function __construct(Specification $one, Specification $other)
    {
        $this->one = $one;
        $this->other = $other;
    }

    public function isSatisfiedBy($candidate): bool
    {
        return $this->one->isSatisfiedBy($candidate) && $this->other->isSatisfiedBy($candidate);
    }
}

class OrSpecification extends AbstractSpecification
{
    private $one;
    private $other;

    public function __construct(Specification $one, Specification $other)
    {
        $this->one = $one;
        $this->other = $other;
    }

    public function isSatisfiedBy($candidate): bool
    {
        return $this->one->isSatisfiedBy($candidate) || $this->other->isSatisfiedBy($candidate);
    }
}

class NotSpecification extends AbstractSpecification
{
    private $wrapped;

    public function __construct(Specification $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    public function isSatisfiedBy($candidate): bool
    {
        return !$this->wrapped->isSatisfiedBy($candidate);
    }
}


///////

class AgeSpecification extends AbstractSpecification
{
    private $minAge;

    public function __construct(int $minAge)
    {
        $this->minAge = $minAge;
    }

    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->age >= $this->minAge;
    }
}

class NameSpecification extends AbstractSpecification
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->name === $this->name;
    }
}

class SurnameSpecification extends AbstractSpecification
{
    private $surname;

    public function __construct(string $surname)
    {
        $this->surname = $surname;
    }

    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->surname === $this->surname;
    }
}

class Person
{
    public function __construct(
        public int $age,
        public string $name,
        public string $surname
    )
    {
    }
}

// Użycie
$ageSpec = new AgeSpecification(18);
$nameSpec = new NameSpecification("John");
$surnameSpec = new SurnameSpecification("Doe");

$combinedSpec = $ageSpec->and($nameSpec)->and($surnameSpec);

$person = new Person(20, "John", "Doe");

$isSatisfied = $combinedSpec->isSatisfiedBy($person); // true

echo $isSatisfied ? "Specyfikacja spełniona" : "Specyfikacja niespełniona"; // Specyfikacja spełniona