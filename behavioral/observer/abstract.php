<?php

class MyObserver1 implements SplObserver {
    public function update(SplSubject $subject): void {
        echo __CLASS__ . ' - ' . $subject->getName();
    }
}

class MyObserver2 implements SplObserver {
    public function update(SplSubject $subject): void {
        echo __CLASS__ . ' - ' . $subject->getName();
    }
}

class MySubject implements SplSubject {
    private $_observers;
    public function __construct(private string $_name) {
        $this->_observers = new SplObjectStorage();
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
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }

    public function getName(): string {
        return $this->_name;
    }
}

$observer1 = new FacebookPublisherObserver();
$observer2 = new InstagramPublisherObserver();

$subject = new MarkdownContentCreator("test");

$subject->attach($observer1);
$subject->attach($observer2);
$subject->notify();