<?php

interface Mediator
{
    public function notify(string $event, array $payload): void;
}

class OrdersModule {
    private Mediator $mediator;
    private array $productsId;

    public function __construct(private string $id, int ...$productId) {
        $this->productsId = $productId;
    }

    public function setMediator(Mediator $mediator) {
        $this->mediator = $mediator;
    }

    public function placeOrder() {
        foreach ($this->productsId as $productId) {
            echo "Dodano produkt o id $productId do zamówienia\n";
        }

        $this->mediator->notify('order:placed', ['productsId' => $this->productsId, 'orderId' => $this->id]);
    }
}

class PaymentModule {
    private Mediator $mediator;

    public function __construct(private string $currency, private float $amount = 0) {
    }

    public function setMediator(Mediator $mediator) {
        $this->mediator = $mediator;
    }

    public function addProductToPay(int $productId): void
    {
        $productPrice = match($productId) {
            1 => 100,
            2 => 200,
            3 => 300
        };

        echo "Dodano produkt o id $productId do płatności na kwotę $productPrice\n";

        $this->amount += $productPrice;
    }

    public function processPayment() {
        echo "Opłacanie zamówienia w walucie $this->currency na kwotę $this->amount\n";

        $this->mediator->notify(
            'payment:processed',
            ['currency' => $this->currency, 'amount' => $this->amount]
        );
    }
}

class NotificationModule {
    private Mediator $mediator;
    private string $orderId;
    private array $products;
    private float $price;
    private string $currency;

    public function __construct(private string $email) {
    }

    public function setMediator(Mediator $mediator) {
        $this->mediator = $mediator;
    }

    public function sendNotification() {
        echo "Na adres ".$this->email." wysłano wiadomość o zakupionych produktach ".$this->getMessage()."\n";

        $this->mediator->notify('notification:sent', ['email' => $this->email]);
    }

    public function getMessage(): string
    {
        $products = join("\n", array_map(fn(string $product) => "Kupiłeś produkt ID: ".$product, $this->products));

        return "Zamówienie ID: ".$this->orderId."\n".$products . "\n" . "Łączna cena: " . $this->price . ' ' . $this->currency;
    }

    public function addOrder(string $orderId, array $productsId): void
    {
        $this->orderId = $orderId;
        $this->products = $productsId;
    }

    public function setPrice(float $price, string $currency): void
    {
        $this->price = $price;
        $this->currency = $currency;
    }
}

class OrderMediator implements Mediator
{
    public function __construct(
        private OrdersModule       $ordersModule,
        private PaymentModule      $paymentModule,
        private NotificationModule $notificationModule
    )
    {
        $this->ordersModule->setMediator($this);
        $this->paymentModule->setMediator($this);
        $this->notificationModule->setMediator($this);
    }

    public function notify(string $event, array $payload): void
    {
        switch ($event) {
            case 'order:placed':
                echo "Po złożeniu zamówienia przechodzę do procesu składania zamówienia\n";
                foreach($payload['productsId'] as $productId) {
                    $this->paymentModule->addProductToPay($productId);
                }

                $this->notificationModule->addOrder($payload['orderId'], $payload['productsId']);
                break;
            case 'payment:processed':
                echo "Po zakończeniu procesu płatności przechodzę do wysyłania powiadomienia\n";
                $this->notificationModule->setPrice($payload['amount'], $payload['currency']);
                break;
        }
    }
}

//// Usage
$ordersModule = new OrdersModule(1, 2, 3);
$paymentModule = new PaymentModule('PLN');
$notificationModule = new NotificationModule('example@example.com');
$mediator = new OrderMediator($ordersModule, $paymentModule, $notificationModule);

$ordersModule->placeOrder();
$paymentModule->processPayment();
$notificationModule->sendNotification();

