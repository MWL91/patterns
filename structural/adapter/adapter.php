<?php

class MySpacePublisher
{
    private $token;

    public function __construct($token)
    {
    }

    public function post($content)
    {
        echo "Sending POST method with content " . $content . " under auth token " . $this->token . "\n";
        return uniqid();
    }

    public function delete($postId)
    {
        echo "Sending DELETE method to " . $postId;
    }
}

interface SocialMediaPublisher
{
    public function create(string $content): string;
    public function edit(string $id, string $content): void;
    public function delete(string $id): void;
}

class InstagramPublisher implements SocialMediaPublisher
{
    public function create(string $content): string
    {
        echo "Creating Instagram post with content " . $content . "\n";
        return uniqid();
    }

    public function edit(string $id, string $content): void
    {
        echo "Editing Instagram post with id " . $id . " and content " . $content . "\n";
    }

    public function delete(string $id): void
    {
        echo "Deleting Instagram post with id " . $id . "\n";
    }
}

// This class is a bit non SRP, but it's just for the sake of the example
function createThenUpdate(SocialMediaPublisher $publisher, string $content): void
{
    $id = $publisher->create($content);
    $publisher->edit($id, $content . ' Update: resolved');
}

class MySpaceAdapter implements SocialMediaPublisher
{
    public function __construct(private MySpacePublisher $mySpacePublisher)
    {
    }

    public function create(string $content): string
    {
        return $this->mySpacePublisher->post($content);
    }

    public function edit(string $id, string $content): void
    {
        $this->mySpacePublisher->delete($id);
        $this->mySpacePublisher->post($content);
    }

    public function delete(string $id): void
    {
        $this->mySpacePublisher->delete($id);
    }
}

function run(string $publisher, string $content) {
    $publisherInstance = match($publisher) {
        'instagram' => new InstagramPublisher(),
        'myspace' => new MySpaceAdapter(new MySpacePublisher('token')),
        default => throw new InvalidArgumentException('Invalid publisher')
    };

    createThenUpdate($publisherInstance, $content);
}

run('instagram', 'Hello world');
run('myspace', 'Hello world');