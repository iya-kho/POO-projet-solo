<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/../src/Defer.php';

use PHPUnit\Framework\TestCase;
use Solo312\Callback;
use Solo312\Defer;

class DeferTest extends TestCase {
    public function testCallbackExecutesCallable(): void {
        $output = '';

        // Define a callable and arguments
        $callback = new Callback(
            function($name) use (&$output) {
                $output .= "Hello, $name!";
            },
            ['World']
        );

        // Call the callback
        $callback->call();

        // Assert the output is correct
        $this->assertEquals("Hello, World!", $output);
    }

    public function testDeferExecutesCallablesInLIFOOrder(): void {
        $output = '';

        // Create a Defer object
        $defer = Defer::init();

        // Add multiple callbacks
        $defer(function() use (&$output) {
            $output .= "First ";
        });
        $defer(function() use (&$output) {
            $output .= "Second ";
        });
        $defer(function() use (&$output) {
            $output .= "Third ";
        });

        // The output should still be empty before destruction
        $this->assertEquals('', $output);

        // Trigger the destructor by unsetting the Defer object
        unset($defer);

        // Assert the output was executed in LIFO order
        $this->assertEquals("Third Second First ", $output);
    }
}
