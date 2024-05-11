<?php

// Interfejs dla odwiedzających
interface FinancialDataVisitor {
    public function visitTransaction(Transaction $transaction): void;
    public function visitBalanceSheet(BalanceSheet $balanceSheet): void;
    public function visitIncomeStatement(IncomeStatement $incomeStatement): void;
}

// Interfejs dla elementów struktury danych
interface FinancialData {
    public function accept(FinancialDataVisitor $visitor): void;
}

// Klasa reprezentująca transakcję finansową
class Transaction implements FinancialData {
    public function __construct(private int $amount)
    {
    }

    public function accept(FinancialDataVisitor $visitor): void
    {
        $visitor->visitTransaction($this);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}

// Klasa reprezentująca bilans
class BalanceSheet implements FinancialData {
    public function __construct(
        private int $assets,
        private int $liabilities
    )
    {
    }

    public function accept(FinancialDataVisitor $visitor): void
    {
        $visitor->visitBalanceSheet($this);
    }

    public function getAssets(): int
    {
        return $this->assets;
    }

    public function getLiabilities(): int
    {
        return $this->liabilities;
    }
}

// Klasa reprezentująca rachunek zysków i strat
class IncomeStatement implements FinancialData {


    public function __construct(
        private int $revenue,
        private int $expenses
    )
    {
    }

    public function accept(FinancialDataVisitor $visitor): void
    {
        $visitor->visitIncomeStatement($this);
    }

    public function getRevenue() {
        return $this->revenue;
    }

    public function getExpenses(): int
    {
        return $this->expenses;
    }
}

// Klasa konkretnej wizyty (odwiedzającego)
class FinancialReportVisitor implements FinancialDataVisitor
{
    public function visitTransaction(Transaction $transaction): void
    {
        echo "Transaction amount: $" . $transaction->getAmount() . "\n";
    }

    public function visitBalanceSheet(BalanceSheet $balanceSheet): void
    {
        echo "Total assets: $" . $balanceSheet->getAssets() . "\n";
        echo "Total liabilities: $" . $balanceSheet->getLiabilities() . "\n";
    }

    public function visitIncomeStatement(IncomeStatement $incomeStatement): void
    {
        echo "Total revenue: $" . $incomeStatement->getRevenue() . "\n";
        echo "Total expenses: $" . $incomeStatement->getExpenses() . "\n";
    }
}

// Kod wykorzystujący wzorzec Visitor
$transaction = new Transaction(1000);
$balanceSheet = new BalanceSheet(5000, 2000);
$incomeStatement = new IncomeStatement(8000, 6000);

$visitor = new FinancialReportVisitor();

$transaction->accept($visitor);
$balanceSheet->accept($visitor);
$incomeStatement->accept($visitor);
