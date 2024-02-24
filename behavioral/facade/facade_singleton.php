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

interface Studio
{
    public function on(): void;
    public function off(): void;
    public function setLightPower(int $power): void;
}

class HardwareStudio implements Studio
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

class StudioFacade
{
    private static Studio $studioHandler;

    private function __construct()
    {
    }

    public static function getInstance(): Studio
    {
        if(!isset(self::$studioHandler)) {
            // In real life this should be get from configuration (e.g. file / database ect.)
            self::$studioHandler = new HardwareStudio(
                [
                    new LightPower('Listwa 220V'),
                    new LightPower('Kontrowe 220V'),
                    new WixLed()
                ],
                new CurtainRemote(),
                new WeatherStation()
            );
        }

        return self::$studioHandler;
    }

    public static function on(): void
    {
        self::getInstance()->on();
    }

    public static function off(): void
    {
        self::getInstance()->off();
    }

    public static function setLightPower(int $power): void
    {
        self::getInstance()->setLightPower($power);
    }

    public static function fake(): void
    {
        self::$studioHandler = new class implements Studio {
            public bool $on = false;
            public int $power = 0;

            public function on(): void
            {
                echo "Fake on\n";
                $this->on = true;

            }

            public function off(): void
            {
                echo "Fake off\n";
                $this->on = false;
            }

            public function setLightPower(int $power): void
            {
                echo "Fake setLightPower\n";
                $this->power = $power;
            }
        };
    }

    public static function assertLightOn(bool $on): void
    {
        if((self::$studioHandler->on ?? null) === null) {
            throw new Exception('You need to call fake method first');
        }

        assert(
            self::$studioHandler->on === $on,
            'Asserting that current '.
            (self::$studioHandler->on ? 'on' : 'off').
            ' light is not '.
            ($on ? 'on' : 'off')
        );
    }

    public static function assertLightPower(int $power): void
    {
        if((self::$studioHandler->power ?? null) === null) {
            throw new Exception('You need to call fake method first');
        }

        assert(
            self::$studioHandler->power === $power,
            'Asserting that current power '. (self::$studioHandler->power).
            ' has '. ($power) . ' volume'
        );
    }
}

StudioFacade::fake();
StudioFacade::on();
StudioFacade::setLightPower(10);

StudioFacade::assertLightOn(true);
StudioFacade::assertLightPower(30);