<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Solo312\FibonacciSequence;

class FibonacciSequenceTest extends TestCase {
    public function testFirst(): void {
        $sequence = FibonacciSequence::first(5);

        $expected = [0, 1, 1, 2, 3];
        $result = [];

        foreach ($sequence as $value) {
            $result[] = $value;
        }

        $this->assertSame($expected, $result);
    }

    public function testRange(): void {
        $sequence = FibonacciSequence::range(5, 5);

        $expected = [5, 8, 13, 21, 34];
        $result = [];

        foreach ($sequence as $value) {
            $result[] = $value;
        }

        $this->assertSame($expected, $result);
    }

    public function testInfiniteRange(): void {
        $sequence = FibonacciSequence::range(10);

        $expected = [55, 89, 144, 233, 377];
        $result = [];
        $i = 0;

        foreach ($sequence as $value) {
            $result[] = $value;
            $i++;
            if ($i >= 5) break; 
        }

        $this->assertSame($expected, $result);
    }

    public function testCacheOptimization(): void {
        $sequence = FibonacciSequence::first(50); 
        $lastValue = null;
        foreach ($sequence as $key => $value) {
            if ($key === 49) {
                $lastValue = $value;
            }
        }

        $this->assertSame(7778742049, $lastValue); // F(50)

        $this->assertSame(12586269025, $sequence->current()); 
    }
}
