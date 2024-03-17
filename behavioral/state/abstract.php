<?php

class ContextHandler
{
    public function __construct(private State $state)
    {
        $this->setState($state);
    }

    public function setState(State $state): void
    {
        echo "State is now handled by " . get_class($state) . ".\n";
        $this->state = $state;
        $this->state->setContext($this);
    }

    public function actionA(): void
    {
        $this->state->publish();
    }

    public function actionB(): void
    {
        $this->state->display();
    }
}

abstract class State
{
    protected BlogPublisher $context;

    public function setContext(BlogPublisher $context)
    {
        $this->context = $context;
    }

    abstract public function executeA(): void;

    abstract public function executeB(): void;
}

class ConcreteStateA extends State
{
    public function publish(): void
    {
        echo "ConcreteStateA executeA.\n";
        echo "ConcreteStateA wants to change the state of the context.\n";
        $this->context->setState(new ReviewingState());
    }

    public function display(): void
    {
        echo "ConcreteStateA executeB.\n";
    }
}

class ConcreteStateB extends State
{
    public function publish(): void
    {
        echo "ConcreteStateB handles executeA.\n";
    }

    public function display(): void
    {
        echo "ConcreteStateB handles executeB.\n";
        echo "ConcreteStateB wants to change the state of the context.\n";
        $this->context->setState(new DraftingState());
    }
}

$context = new BlogPublisher(new DraftingState());
$context->publish();
$context->display();