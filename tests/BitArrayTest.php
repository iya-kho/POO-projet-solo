<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/../src/Defer.php';

use PHPUnit\Framework\TestCase;
use Solo312\BitArray;

class BitArrayTest extends TestCase {
    public function testBitArrayInitialization(): void {
        $bits = new BitArray([1, 0, 1, 1, 0, false, true]);
        
        $this->assertEquals(7, count($bits)); // Check the count of bits
        $this->assertEquals(1, $bits[0]);    // First bit
        $this->assertEquals(0, $bits[1]);    // Second bit
        $this->assertEquals(0, $bits[5]);    // Boolean true as 1
        $this->assertEquals(1, $bits[6]);    // Boolean false as 0
    }

    public function testFromString(): void {
        $bitArray = BitArray::fromString("0b101101");
        $this->assertEquals(6, count($bitArray));
        $this->assertEquals("0b101101", (string)$bitArray); // String representation
    }

    public function testFromInt(): void {
        $bitArray = BitArray::fromInt(29); // 29 in binary is "11101"
        $this->assertEquals(5, count($bitArray));
        $this->assertEquals("0b11101", (string)$bitArray); // String representation
    }

    public function testSetBits(): void {
        $bits = new BitArray();
        $bits->set([1, 0, 1], 3); // Set bits starting from index 3
        $this->assertEquals(6, count($bits)); // Count should reflect the highest bit index + 1
        $this->assertEquals("0b101000", (string)$bits); // Expected binary representation
    }

    public function testUnset(): void {
        // Initialize a BitArray
        $bits = new BitArray([1, 1, 1, 1, 1, 1]);
    
        // Test clearing a positive size range
        $bits->unset(2, 2); // Clear 2 bits starting from index 2
        $this->assertEquals("0b110011", (string)$bits); // Expected binary string
    
        // Test clearing with a negative size
        $bits->unset(4, -2); // Clear from index 4 to the end minus 2 bits
        $this->assertEquals("0b11", (string)$bits); // Expected binary string
    
        // Test clearing with size 0 (no changes)
        $bits->unset(3, 0); // Should do nothing
        $this->assertEquals("0b11", (string)$bits); // No change
    
        // Test clearing the entire array
        $bits->unset(0, -1); // Clear all bits
        $this->assertEquals("0b0", (string)$bits); // Expected empty array
    }    

    public function testSlice(): void {
        $bits = new BitArray([1, 0, 1, 1, 0, 1, 0, 1]);
        $slice = $bits->slice(2, 4); // Extract 4 bits starting from index 2
        $this->assertEquals(4, count($slice));
        $this->assertEquals("0b1011", (string)$slice); // Expected slice representation
    }

    public function testIterator(): void {
        $bits = new BitArray([1, 0, 1, 1, 0]);
        $iterator = $bits->getIterator();

        $expectedBits = [1, 0, 1, 1, 0];
        $index = 0;

        foreach ($iterator as $key => $bit) {
            $this->assertEquals($index, $key); // Verify the key
            $this->assertEquals($expectedBits[$index], $bit); // Verify the bit value
            $index++;
        }
    }

    public function testToString(): void {
        $bits = new BitArray([1, 0, 1, 1, 0]);
        $this->assertEquals("0b1101", (string)$bits); // Ensure string representation matches
    }
}