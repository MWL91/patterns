<?php

class AccountBalance
{
    public function __construct(private string $balanceId, private int $money = 0)
    {
        echo "Originator: My initial balance is: {$this->money}zł\n";
    }

    public function transaction(int $amount): void
    {
        echo "Originator: Deposit created for amount of {$amount}zł\n";
        $this->balanceId = uniqid();
        $this->money += $amount;

        if($this->money < 0) {
            throw new OutOfRangeException("Insufficient balance");
        }

        echo "Originator: My new balance #{$this->balanceId} is: {$this->money}zł\n";
    }

    public function save(): BalanceMemento
    {
        return new BalanceMemento(
            $this->balanceId,
            $this->money
        );
    }

    public function restore(BalanceMemento $memento): void
    {
        $this->balanceId = $memento->getBalanceId();
        $this->money = $memento->getAmount();

        echo "Originator: My state has changed to #{$this->balanceId} - {$this->money}zł\n";
    }
}

class BalanceMemento
{
    private DateTimeInterface $date;

    public function __construct(
        readonly private string $balanceId,
        readonly private int $amount
    )
    {
        $this->date = new DateTime();
    }

    public function getBalanceId(): string
    {
        return $this->balanceId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getDate(): string
    {
        return $this->date->format('Y-m-d H:i:s');
    }
}

class Caretaker
{
    /**
     * @var Memento[]
     */
    private array $mementos = [];

    public function __construct(private AccountBalance $originator)
    {
    }

    public function backup(): void
    {
        // Here I have only what provided as public
        echo "Caretaker: Saving Originator's state...\n";
        $this->mementos[] = $this->originator->save();
    }

    public function undo(): void
    {
        if (!count($this->mementos)) {
            return;
        }
        $memento = array_pop($this->mementos);

        echo "Caretaker: Restoring to #" . $memento->getBalanceId() . "\n";
        try {
            $this->originator->restore($memento);
        } catch (\Exception $e) {
            $this->undo();
        }
    }
}

/**
 * Client code.
 */
$originator = new AccountBalance(uniqid(), 0);
$caretaker = new Caretaker($originator);

$caretaker->backup();

foreach([100, 50, -500, -100] as $transaction) {
    try {
        $originator->transaction($transaction);
        $caretaker->backup();
    } catch (\Exception $e) {
        echo "ERROR: ".$e->getMessage()."\n";
        $caretaker->undo();
    }
}