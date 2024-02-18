<?php
class ImmutableClass {
    public string $data;

    public function __construct($data) {
        $this->data = $data;
    }
}

function short(ImmutableClass $obj): string
{
    $obj->data = substr($obj->data, 0, 3);
    return $obj->data;
}

$obj1 = new ImmutableClass("Original");

echo short(clone $obj1); // Output: Ori
echo $obj1->data; // Output: Original - no changes thanks to clone
