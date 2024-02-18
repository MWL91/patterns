<?php
class MyClass {
    public $data = 'default';

    public function __clone() {
        $this->data = 'cloned';
    }
}

$obj1 = new MyClass();
$obj2 = clone $obj1;

echo $obj1->data; // Output: default
echo $obj2->data; // Output: cloned