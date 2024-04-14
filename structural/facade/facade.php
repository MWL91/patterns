<?php

interface Light
{
    public function turnOn(): void;
    public function turnOff(): void;
    public function setPower(int $power): void;
}

interface Curtain
{
    public function open(): void;
    public function close(): void;
}

class LightPower implements Light
{
    public function __construct(private string $powerName)
    {
    }

    public function turnOn(): void
    {
        echo "Turn on Light at power ".$this->powerName."\n";
    }

    public function turnOff(): void
    {
        echo "Turn off Light at power ".$this->powerName."\n";
    }

    public function setPower(int $power): void
    {
        echo "Update Light power at ".$this->powerName." for ".$power."%\n";
    }
}

class WixLed implements Light
{
    public function turnOn(): void
    {
        echo "Turn on Wix LED\n";
    }

    public function turnOff(): void
    {
        echo "Turn off Wix LED\n";
    }

    public function setPower(int $power): void
    {
        echo "Update Wix LED power for ".$power."%\n";
    }
}

class CurtainRemote implements Curtain
{
    public function open(): void
    {
        echo "Open courtine\n";
    }

    public function close(): void
    {
        echo "Close courtine\n";
    }
}

interface Weather
{
    public function isSunny(): bool;
}

class WeatherStation implements Weather
{
    public function isSunny(): bool
    {
        return rand(0, 1);
    }
}

class Studio
{
    /**
     * @var Light[] $lights
     */
    public function __construct(
        protected array   $lights,
        protected Curtain $curtains,
        protected Weather $weather
    ) {
    }

    public function on(): void
    {
        if($this->weather->isSunny()) {
            $this->curtains->close();
        }

        foreach($this->lights as $light) {
            $light->turnOn();
        }
    }

    public function off(): void
    {
        $this->curtains->open();
        foreach($this->lights as $light) {
            $light->turnOff();
        }
    }

    public function setLightPower(int $power): void
    {
        foreach($this->lights as $light) {
            $light->setPower($power);
        }
    }
}

$lights = [
    new LightPower('Listwa 220V'),
    new LightPower('Kontrowe 220V'),
    new WixLed()
];
$curtain = new CurtainRemote();
$weather = new WeatherStation();

$studio = new Studio($lights, $curtain, $weather);
$studio->on();