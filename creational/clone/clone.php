<?php
class MyClass {
    public string $data;
}

$obj1 = new MyClass();
$obj1->data = "Hello";

// Tworzymy kopię obiektu $obj1
$obj2 = clone $obj1;
$obj3 = $obj1;

echo $obj1->data; // Output: Hello
echo $obj2->data; // Output: Hello
echo $obj3->data; // Output: Hello

$obj2->data = "World";

echo $obj1->data; // Output: Hello
echo $obj2->data; // Output: World
echo $obj3->data; // Output: Hello
