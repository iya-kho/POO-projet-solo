<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/../src/Defer.php';

use PHPUnit\Framework\TestCase;
use Solo312\BitArray;

class BitArrayTest extends TestCase {
    public function testBitArrayInitialization(): void {
        $bits = new BitArray([1, 0, 1, 1, 0, false, true]);
        
        $this->assertEquals(7, count($bits));
        $this->assertEquals(1, $bits[0]);
        $this->assertEquals(0, $bits[1]);
        $this->assertEquals(0, $bits[5]);
        $this->assertEquals(1, $bits[6]); 
    }

    public function testFromString(): void {
        $bitArray = BitArray::fromString("0b101101");
        $this->assertEquals(6, count($bitArray));
        $this->assertEquals("0b101101", (string)$bitArray);
    }

    public function testFromInt(): void {
        $bitArray = BitArray::fromInt(29);
        $this->assertEquals(5, count($bitArray));
        $this->assertEquals("0b11101", (string)$bitArray);
    }

    public function testSetBits(): void {
        $bits = new BitArray();
        $bits->set([1, 0, 1], 3);
        $this->assertEquals(6, count($bits)); 1;
        $this->assertEquals("0b101000", (string)$bits);
    }

    public function testUnset(): void {
        $bits = new BitArray([1, 1, 1, 1, 1, 1]);
    
        $bits->unset(2, 2); 
        $this->assertEquals("0b110011", (string)$bits); 
    
        $bits->unset(4, -2); 
        $this->assertEquals("0b11", (string)$bits);
    
        $bits->unset(3, 0);
        $this->assertEquals("0b11", (string)$bits); 
    
        $bits->unset(0, -1);
        $this->assertEquals("0b0", (string)$bits); 
    }    

    public function testSlice(): void {
        $bits = new BitArray([1, 0, 1, 1, 0, 1, 0, 1]);
        $slice = $bits->slice(2, 4); 
        $this->assertEquals(4, count($slice));
        $this->assertEquals("0b1011", (string)$slice);
    }

    public function testIterator(): void {
        $bits = new BitArray([1, 0, 1, 1, 0]);
        $iterator = $bits->getIterator();

        $expectedBits = [1, 0, 1, 1, 0];
        $index = 0;

        foreach ($iterator as $key => $bit) {
            $this->assertEquals($index, $key);
            $this->assertEquals($expectedBits[$index], $bit);
            $index++;
        }
    }

    public function testToString(): void {
        $bits = new BitArray([1, 0, 1, 1, 0]);
        $this->assertEquals("0b1101", (string)$bits);
    }
}