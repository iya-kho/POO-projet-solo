<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Solo312\Defer;

function someFunction() {
    $a = Defer::init();

    $a(function($name) {
        echo "Goodbye, $name!" . PHP_EOL;
    }, ['Alice']);

    $a(function($x, $y) {
        echo "$x + $y = " . ($x + $y) . PHP_EOL;
    }, [2, 3]);

    echo "End of function <br>";
}

someFunction();
