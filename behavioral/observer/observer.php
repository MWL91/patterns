<?php

class MarkdownContentCreator implements SplSubject {
    private SplObjectStorage $_observers;
    private bool $published = false;

    public function __construct(private string $name, private string $content) {
        $this->_observers = new SplObjectStorage();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function markdownToHtml() {
        // Zastąpienie nagłówków
        $this->content = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $this->content);
        $this->content = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $this->content);
        $this->content = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $this->content);
        // Zastąpienie tekstu pogrubionego
        $this->content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $this->content);
        // Zastąpienie tekstu pochylonego
        $this->content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $this->content);
        // Zastąpienie list
        $this->content = preg_replace('/^\d\. (.*)$/m', '<li>$1</li>', $this->content);
        $this->content = preg_replace('/<li>(.*?)<\/li>/', '<ul><li>$1</li></ul>', $this->content);
        // Zastąpienie kodu
        $this->content = preg_replace('/```(.+?)```/s', '<code>$1</code>', $this->content);
        // Zastąpienie linków
        $this->content = preg_replace('/\[([^\[]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $this->content);
    }

    public function publish(): void
    {
        $this->markdownToHtml();
        echo 'Treść ' . substr($this->content, 0, 15) . ' została opublikowana' . PHP_EOL;
        $this->published = true;
    }

    public function attach(SplObserver $observer): void
    {
        $this->_observers->attach($observer);
    }

    public function detach(SplObserver $observer): void
    {
        $this->_observers->detach($observer);
    }

    public function notify(): void
    {
        if(!$this->published) {
            return;
        }

        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }
}

class FacebookPublisherObserver implements SplObserver {
    public function update(SplSubject $subject): void {
        echo 'Dodaję post na facebooka o nazwie ' . $subject->getName() . PHP_EOL;
    }
}

class InstagramPublisherObserver implements SplObserver {
    public function update(SplSubject $subject): void {
        echo 'Dodaję zdjęcie na instagrama o nazwie ' . $subject->getName() . PHP_EOL;
    }
}

$publisher = new MarkdownContentCreator("Tytuł", "## Nagłówek 2\n**Pogrubienie**\n*Pochylenie*\n1. Lista\n2. Lista\n```php\n<?php\n```");

$observer1 = new FacebookPublisherObserver();
$observer2 = new InstagramPublisherObserver();

$publisher->attach($observer1);
$publisher->attach($observer2);
$publisher->publish();
$publisher->notify();