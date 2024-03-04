<?php

enum Transport: string
{
    case PLANE = 'plane';
    case CAR = 'car';
}

class Holiday {

    public function __construct(
        private DateTimeInterface $from,
        private DateTimeInterface $to,
        private string $location,
        private int $distance,
        private ?string $hotel = null,
        private ?Transport $transport = null
    )
    {
    }

    public function getFrom(): DateTimeInterface
    {
        return $this->from;
    }

    public function getTo(): DateTimeInterface
    {
        return $this->to;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function getDuration(): int
    {
        return $this->from->diff($this->to)->days;
    }

    public function setHotel(string $hotel): void
    {
        $this->hotel = $hotel;
    }

    public function setTransport(Transport $transport): void
    {
        $this->transport = $transport;
    }

    public function getTransport(): Transport
    {
        return $this->transport;
    }
}

interface HolidayHandler
{
    public function setNext(HolidayHandler $handler): HolidayHandler;

    public function handle(Holiday $request): ?Holiday;
}

abstract class AbstractHolidayHandler implements HolidayHandler
{
    private HolidayHandler $nextHandler;

    public function setNext(HolidayHandler $handler): HolidayHandler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(Holiday $request): ?Holiday
    {
        if (isset($this->nextHandler)) {
            return $this->nextHandler->handle($request);
        }

        return null;
    }
}

class VacationAvailabilityHandler extends AbstractHolidayHandler
{
    public function handle(Holiday $request): ?Holiday
    {
        $daysOfVacation = $this->getDaysOfVacation();

        if($daysOfVacation <= $request->getDuration()) {
            throw new OutOfRangeException('The duration of the vacation is less than the days of vacation');
        }

        return parent::handle($request);
    }

    private function getDaysOfVacation(): int
    {
        return rand(30, 60);
    }
}

class HotelBookingHandler extends AbstractHolidayHandler
{
    public function handle(Holiday $request): ?Holiday
    {
        $this->bookHotel($request);

        return parent::handle($request);
    }

    private function bookHotel(Holiday $request): void
    {
        $hotelName = ["Grand Budapest", "Belvedere", "Namiot"][rand(0, 2)];
        echo "Zarezerwowano ".$hotelName." w ".$request->getLocation()." na ".$request->getDuration()." dni\n";
        $request->setHotel($hotelName);
    }
}

class FlyReservationHandler extends AbstractHolidayHandler
{
    public function __construct(private int $minDistance = 500)
    {
    }

    public function handle(Holiday $request): ?Holiday
    {
        if($request->getDistance() > $this->minDistance) {
            echo "Rezerwuję lot bo odległość to ".$request->getDistance()." km\n";
            $request->setTransport(Transport::PLANE);
        }

        return parent::handle($request);
    }
}

class FuelHandler extends AbstractHolidayHandler
{
    public function __construct(private int $maxDistance = 500)
    {
    }

    public function handle(Holiday $request): ?Holiday
    {
        if($request->getDistance() <= $this->maxDistance) {
            echo "Tankuję auto bo odległość to ".$request->getDistance()." km\n";
            $request->setTransport(Transport::CAR);
        }

        return parent::handle($request);
    }
}

class CarLeaseHandler extends AbstractHolidayHandler
{
    public function handle(Holiday $request): ?Holiday
    {
        if($request->getTransport() === Transport::PLANE) {
            echo "Wypożyczam auto bo lecimy samolotem\n";
        }

        return parent::handle($request);
    }
}

$holiday = new Holiday(
    new DateTime('2022-07-01'),
    new DateTime('2022-07-15'),
    'Greece',
    1000
);

$vacation = new VacationAvailabilityHandler();
$hotel = new HotelBookingHandler();
$fly = new FlyReservationHandler(500);
$fuel = new FuelHandler(500);
$car = new CarLeaseHandler();

$vacation
    ->setNext($hotel)
    ->setNext($fly)
    ->setNext($fuel)
    ->setNext($car);

$vacation->handle($holiday);

echo "------\n";

// Tylko transport i wynajem
$fly->handle($holiday);