<?php

class MyClass {
    public SomeOtherClass $data;

    public function __clone() {
        // Przykład głębokiej kopii, kopiujemy również obiekt $data
        $this->data = clone $this->data;
    }
}

class SomeOtherClass {
    public $data;
}

$obj1 = new MyClass();
$obj1->data = new SomeOtherClass();
$obj1->data->data = "Hello";

$obj2 = clone $obj1;
$obj3 = $obj1;
$obj1->data->data = "World";

echo $obj1->data->data; // Output: World
echo $obj2->data->data; // Output: Hello
echo $obj3->data->data; // Output: World
