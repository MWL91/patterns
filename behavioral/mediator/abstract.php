<?php

interface Mediator
{
    public function notify(object $sender, string $event): void;
}

class ConcreteMediator implements Mediator
{
    public function __construct(private ClassA $component1, private ClassB $component2)
    {
        $this->component1->setMediator($this);
        $this->component2->setMediator($this);
    }

    public function notify(object $sender, string $event): void
    {
        if ($event == "A") {
            echo "Mediator reacts on A and triggers following operations:\n";
            $this->component2->doC();
        }

        if ($event == "D") {
            echo "Mediator reacts on D and triggers following operations:\n";
            $this->component1->doB();
            $this->component2->doC();
        }
    }
}

class BaseComponent
{
    public function __construct(protected ?Mediator $mediator = null)
    {
    }

    public function setMediator(Mediator $mediator): void
    {
        $this->mediator = $mediator;
    }
}

class ClassA extends BaseComponent
{
    public function doA(): void
    {
        echo "ClassA does A.\n";
        $this->mediator->notify($this, "A");
    }

    public function doB(): void
    {
        echo "ClassA does B.\n";
        $this->mediator->notify($this, "B");
    }
}

class ClassB extends BaseComponent
{
    public function doC(): void
    {
        echo "ClassB does C.\n";
        $this->mediator->notify($this, "C");
    }

    public function doD(): void
    {
        echo "ClassB does D.\n";
        $this->mediator->notify($this, "D");
    }
}

$c1 = new ClassA();
$c2 = new ClassB();
$mediator = new ConcreteMediator($c1, $c2);

$c1->doA();
echo PHP_EOL;
$c2->doD();