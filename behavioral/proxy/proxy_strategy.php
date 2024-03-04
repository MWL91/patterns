<?php

interface WeaponProductsListing
{
    public function get(): array;
}

interface WeaponListStrategy extends WeaponProductsListing
{
    public function isSatisfiedBy(User $user): bool;
}

final class LegalWeapons implements WeaponListStrategy
{
    public function get(): array
    {
        return [
            'Gas',
            'Knife',
            'Tranquilizer',
        ];
    }

    public function isSatisfiedBy(User $user): bool
    {
        return true;
    }
}

final class LicencedWeapons implements WeaponListStrategy
{
    public function get(): array
    {
        return [
            'Beretta',
            'Glock',
            'Smith & Wesson',
        ];
    }

    public function isSatisfiedBy(User $user): bool
    {
        return (bool)$user->getLicenceDate();
    }
}

final class PremiumWeapons implements WeaponListStrategy
{
    public function get(): array
    {
        return [
            'M16',
            'AK-47',
            'M4A1',
        ];
    }

    public function isSatisfiedBy(User $user): bool
    {
        // Must have 30 days of licence
        return $user->getLicenceDate() && $user->getLicenceDate()->diff(new DateTime())->days > 30;
    }
}

class User
{
    public function __construct(
        private ?DateTimeInterface $licenceDate = null
    )
    {
    }

    public function getLicenceDate(): ?DateTimeInterface
    {
        return $this->licenceDate;
    }
}

class WeaponListing implements WeaponProductsListing
{
    private array $listings;

    public function __construct(
        private User $user,
        array $listings,
    )
    {
        foreach($listings as $listing) {
            if(!$listing instanceof WeaponListStrategy) {
                throw new InvalidArgumentException('Invalid listing');
            }
        }

        $this->listings = $listings;
    }

    public function get(): array
    {
        $productList = [];
        foreach($this->listings as $listing) {
            if($listing->isSatisfiedBy($this->user)) {
                $productList = [...$productList, ...$listing->get()];
            }
        }

        return $productList;
    }
}

$premiumUser = new User(new DateTime('2021-01-01'));
$newLicencedUser = new User(new DateTime());
$nonLicencedUser = new User();
$listOfWeapons = [
    new LegalWeapons(),
    new LicencedWeapons(),
    new PremiumWeapons(),
];

$premiumUserWeaponListing = new WeaponListing($premiumUser, $listOfWeapons);
echo implode(', ', $premiumUserWeaponListing->get());

echo "\n----\n";

$newLicencedUserWeaponListing = new WeaponListing($newLicencedUser, $listOfWeapons);
echo implode(', ', $newLicencedUserWeaponListing->get());

echo "\n----\n";

$premiumUserWeaponListing = new WeaponListing($nonLicencedUser, $listOfWeapons);
echo implode(', ', $premiumUserWeaponListing->get());