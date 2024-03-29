<?php

interface Command
{
    public function execute(): void;
}

class FileStorageCommand implements Command
{
    public function __construct(private string $message)
    {
    }

    public function execute(): void
    {
        echo "Store message to file: $this->message" . PHP_EOL;
        file_put_contents('message.txt', $this->message);
    }
}

class InMemoryStorageCommand implements Command
{
    public function __construct(private string $message)
    {
    }

    public function execute(): void
    {
        echo "Memory has message: $this->message" . PHP_EOL;
    }
}

class MailMessageCommand implements Command
{
    public function __construct(private string $email, private string $message)
    {
    }

    public function execute(): void
    {
        echo "Send message to email: $this->email" . PHP_EOL;
        mail($this->email, 'New message', $this->message);
    }
}

class SlackHook
{
    public function __construct(private string $hook)
    {
    }

    public function send(string $message): void
    {
        echo "POST $message to " . $this->hook . PHP_EOL;
    }
}

class SlackMessageCommand implements Command
{
    public function __construct(private SlackHook $hook, private string $message)
    {
    }

    public function execute(): void
    {
        echo "Send message to slack: ".$this->message . PHP_EOL;
        $this->hook->send($this->message);
    }
}

class CreateMessageInvoker
{
    private Command $storeCommand;
    private Command $notifyCommand;

    public function setStoreCommand(Command $store): void
    {
        $this->storeCommand = $store;
    }

    public function setNotifyCommand(Command $notify): void
    {
        $this->notifyCommand = $notify;
    }

    public function __invoke(): void
    {
        $this->storeCommand->execute();

        if(isset($this->notifyCommand) && $this->notifyCommand instanceof Command) {
            $this->notifyCommand->execute();
        }
    }
}

$handler = new CreateMessageInvoker();

$message = "First message";
$handler->setStoreCommand(new FileStorageCommand($message));
$handler->setNotifyCommand(new MailMessageCommand('marcin@lenkowski.net', $message));

$handler();

echo "////////////////////////\n";

$handler = new CreateMessageInvoker();

$message = "Second message";
$handler->setStoreCommand(new InMemoryStorageCommand($message));
$slackHook = new SlackHook('https://hooks.slack.com/services/123456789/123456789/123456789');
$handler->setNotifyCommand(new SlackMessageCommand($slackHook, $message));

$handler();

echo "////////////////////////\n";

$handler = new CreateMessageInvoker();

$message = "Third message";
$handler->setStoreCommand(new FileStorageCommand($message));
// no notify command

$handler();
