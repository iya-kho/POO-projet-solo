<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/../src/Defer.php';

use PHPUnit\Framework\TestCase;
use Solo312\Callback;
use Solo312\Defer;

class DeferTest extends TestCase {
    public function testCallbackExecutesCallable(): void {
        $output = '';

        $callback = new Callback(
            function($name) use (&$output) {
                $output .= "Hello, $name!";
            },
            ['World']
        );

        $callback->call();

        $this->assertEquals("Hello, World!", $output);
    }

    public function testDeferExecutesCallablesInLIFOOrder(): void {
        $output = '';

        $defer = Defer::init();

        $defer(function() use (&$output) {
            $output .= "First ";
        });
        $defer(function() use (&$output) {
            $output .= "Second ";
        });
        $defer(function() use (&$output) {
            $output .= "Third ";
        });

        $this->assertEquals('', $output);

        unset($defer);

        $this->assertEquals("Third Second First ", $output);
    }
}
