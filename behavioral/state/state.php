<?php

interface Publicator
{
    public function publish(): void;
    public function display(): void;
}

interface Rollbackable
{
    public function rollback(): void;
}

class BlogPublisher implements Publicator, Rollbackable
{
    private State $state;

    public function __construct(private string $content, ?State $state = null)
    {
        $state ??= new DraftingState();
        $this->setState($state);
    }

    public function setState(State $state): void
    {
        echo "State has been changed to " . get_class($state) . ".\n";
        $this->state = $state;
        $this->state->setContext($this);
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function publish(): void
    {
        $this->state->publish();
    }

    public function display(): void
    {
        $this->state->display();
    }

    public function rollback(): void
    {
        if ($this->state instanceof Rollbackable) {
            $this->state->rollback();
        }
    }
}

abstract class State implements Publicator
{
    protected BlogPublisher $context;

    public function setContext(BlogPublisher $context)
    {
        $this->context = $context;
    }
}

class DraftingState extends State
{
    public function publish(): void
    {
        echo "Publishing draft";
        $this->context->setState(new ReviewingState());
        echo "Content '".substr($this->context->getContent(), 0, 10)."...' is ready to be reviewed.\n";
    }

    public function display(): void
    {
        echo "404\n";
    }
}

class ReviewingState extends State implements Rollbackable
{
    public function publish(): void
    {
        echo "Publishing after review\n";
        $this->context->setState(new PublishingState());
        echo "Content '".substr($this->context->getContent(), 0, 10)."...' is ready to be reviewed.\n";
    }

    public function display(): void
    {
        echo "Content comming soon...\n";
    }

    public function rollback(): void
    {
        $this->context->setState(new DraftingState());
    }
}

class PublishingState extends State implements Rollbackable
{
    public function publish(): void
    {
        echo "Content '".substr($this->context->getContent(), 0, 10)."...' is published.\n";
    }

    public function display(): void
    {
        echo $this->context->getContent();
    }

    public function rollback(): void
    {
        $this->context->setState(new DraftingState());
    }
}

$context = new BlogPublisher('sPeCial CONtenT');

// publikujemy tekst w stanie draft i go wyświetlamy
$context->display();
$context->publish();

// Przeprowadzamy review, nie jesteśmy z niego zadowoleni
echo "Sprawdzam: ".substr($context->getContent(), 0, 10)."...\n";
$context->rollback();

// Poprawiamy treść i ponownie publikujemy, oddając do oceny
$context->setContent('Special content');
$context->publish();
$context->display();

// Przeprowadzamy review i akceptujemy
$context->setContent('Special content after review.');
$context->publish();
echo "\n-----------------\n";
$context->display();
