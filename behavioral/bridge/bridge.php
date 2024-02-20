<?php

interface Device
{
    public function isEnabled(): bool;

    public function enable(): void;

    public function disable(): void;

    public function getVolume(): int;

    public function setVolume(int $percent): void;

    public function getChannel(): int;

    public function setChannel(int $channel): void;
}

interface PointerDevice extends Device
{
    public function move(int $x, int $y): void;

    public function click(): void;

    public function getMove(): array;
}

class Television implements PointerDevice
{
    private bool $isEnabled = false;
    private int $volume = 50;
    private int $channel = 1;
    private array $move = [0, 0];

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function enable(): void
    {
        echo "Turning on the television\n";
        $this->isEnabled = true;
    }

    public function disable(): void
    {
        echo "Turning off the television\n";
        $this->isEnabled = false;
    }

    public function getVolume(): int
    {
        return $this->volume;
    }

    public function setVolume(int $percent): void
    {
        echo "Setting volume to " . $percent . "\n";
        $this->volume = $percent;
    }

    public function getChannel(): int
    {
        return $this->channel;
    }

    public function setChannel(int $channel): void
    {
        echo "Setting channel to " . $channel . "\n";
        $this->channel = $channel;
    }

    public function move(int $x, int $y): void
    {
        echo "Moving to position: " . $x . ", " . $y . "\n";
        $this->move = [$x, $y];
    }

    public function click(): void
    {
        echo "Clicking on position: " . implode(', ', $this->move) . "\n";
    }

    public function getMove(): array
    {
        return $this->move;
    }
}

interface BothFeatures extends Device, PointerDevice
{
}

class Radio implements Device
{
    private bool $isEnabled = false;
    private int $volume = 50;
    private int $channel = 1;

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function enable(): void
    {
        echo "Turning on the radio\n";
        $this->isEnabled = true;
    }

    public function disable(): void
    {
        echo "Turning off the radio\n";
        $this->isEnabled = false;
    }

    public function getVolume(): int
    {
        return $this->volume;
    }

    public function setVolume(int $percent): void
    {
        echo "Setting radio volume to " . $percent . "\n";
        $this->volume = $percent;
    }

    public function getChannel(): int
    {
        return $this->channel;
    }

    public function setChannel(int $channel): void
    {
        echo match($channel) {
            1 => "Setting radio channel to RMF\n",
            2 => "Setting radio channel to Z\n",
            3 => "Setting radio channel to Eska\n",
            default => "Setting radio to ".$channel."MHz\n",
        };
        $this->channel = $channel;
    }
}

class Remote
{
    public function __construct(private Device $device)
    {
    }

    public function turnOn(): void
    {
        $this->device->enable();
    }

    public function turnOff(): void
    {
        $this->device->disable();
    }

    public function setChannel(int $channel): void
    {
        $this->device->setChannel($channel);
    }

    public function setVolume(int $percent): void
    {
        $this->device->setVolume($percent);
    }
}

function runAndChangeChanel(Device $device)
{
    $remote = new Remote($device);
    $remote->turnOn();
    $remote->setChannel(1);
    $remote->setVolume(50);
}

runAndChangeChanel(new Television());
echo PHP_EOL;
runAndChangeChanel(new Radio());

class Mouse
{
    public function __construct(private Device $device)
    {
    }

    public function run(): void
    {
        $this->device->enable();
        $this->device->setVolume(100);
    }

    public function move(int $x, int $y): void
    {
        if(!($this->device instanceof PointerDevice)) return;

        $this->device->move($x, $y);
    }

    public function click(): void
    {
        if(!($this->device instanceof PointerDevice)) return;

        $this->device->click();
    }
}

echo '---' . PHP_EOL;

function runAndMove(Device $device)
{
    $remote = new Mouse($device);
    $remote->run();
    $remote->click();
    $remote->move(100, 200);
}

runAndMove(new Television());
echo PHP_EOL;
runAndMove(new Radio());