<?php declare(strict_types=1);

namespace Solo312;

class BitArrayIterator implements \Iterator {
    private BitArray $bitArray; // Reference to the BitArray being iterated
    private int $position = 0; // Current position in the BitArray

    public function __construct(BitArray $bitArray) {
        $this->bitArray = $bitArray;
    }

    // Returns the current bit at the position
    public function current(): int {
        return $this->bitArray[$this->position];
    }

    // Returns the current position (key)
    public function key(): int {
        return $this->position;
    }

    // Moves to the next bit
    public function next(): void {
        $this->position++;
    }

    // Resets the position to the start of the BitArray
    public function rewind(): void {
        $this->position = 0;
    }

    // Checks if the current position is valid
    public function valid(): bool {
        return $this->position < count($this->bitArray);
    }
}

class BitArray implements \ArrayAccess, \Countable, \IteratorAggregate, \Stringable {
    private const BYTE_SIZE = 8;
    private const INT_SIZE = PHP_INT_SIZE * self::BYTE_SIZE;

    private array $data = [];
    private int $bitCount = 0;

    public function __construct(array $bits = []) {
        foreach ($bits as $bit) {
            // Validate and normalize the bit
            if ($bit !== 0 && $bit !== 1 && $bit !== true && $bit !== false) {
                throw new \InvalidArgumentException("Bits must be 0, 1, true, or false.");
            }
            // Normalize booleans to integers
            $bit = (int) $bit;

            // Add the bit
            $this->addBit($bit);
        }
    }

    // Static constructor from an integer
    public static function fromInt(int $number): self {
        $binary = decbin($number); // Convert integer to binary string
        return self::fromString($binary);
    }

    // Static constructor from a binary string
    public static function fromString(string $binary): self {
        $bitArray = new self();
        $binary = ltrim($binary, "0b"); // Remove "0b" prefix if present
        $length = strlen($binary);

        // Add each bit to the BitArray
        for ($i = 0; $i < $length; $i++) {
            $bitArray[$i] = (int) $binary[$length - $i - 1]; // Read bits from right to left
        }

        return $bitArray;
    }

    public function slice(int $offset, ?int $length = null): self {
        $slice = new self(); // Create a new BitArray to hold the slice
    
        // Handle negative offset or offset beyond the bounds
        if ($offset < 0) {
            $offset = max(0, $this->count() + $offset);
        } elseif ($offset >= $this->count()) {
            return $slice; // Return empty BitArray if offset is out of range
        }
    
        // Calculate the length of the slice
        $end = $length === null
            ? $this->count() // If no length, go to the end
            : ($length < 0
                ? max(0, $this->count() + $length) // Handle negative length
                : min($this->count(), $offset + $length)); // Cap at the total count
    
        // Extract bits and populate the new BitArray
        for ($i = $offset; $i < $end; $i++) {
            $slice[$i - $offset] = $this[$i]; // Copy bits to the new BitArray
        }
    
        return $slice; // Return the resulting BitArray slice
    }    

    public function set(array $bits, int $offset = 0): void {
        foreach ($bits as $index => $bit) {
            if ($bit !== 0 && $bit !== 1) {
                throw new \InvalidArgumentException("Only 0 or 1 are allowed.");
            }
            $this[$offset + $index] = $bit; // Set each bit at the appropriate position
        }
    }    

    public function unset(int $offset, int $size): void {
        // Handle the case where size is 0: do nothing
        if ($size === 0) {
            return;
        }
    
        // Calculate the end position based on the size
        $end = $size > 0 
            ? $offset + $size                  // Positive size: Clear up to offset + size
            : $this->count();                  // Negative size: Clear up to the end of the BitArray
    
        // Ensure the range is valid
        $offset = max(0, $offset);             // Offset cannot be negative
        $end = max(0, $end);                   // End cannot be negative
    
        // Clear bits from offset to end
        for ($i = $offset; $i < $end; $i++) {
            if ($i < $this->count()) {
                $this[$i] = 0; // Set the bit to 0
            }
        }
    
        // Update bitCount to ignore trailing zeros
        $this->bitCount = 0;
        foreach ($this->data as $index => $value) {
            if ($value !== 0) {
                $this->bitCount = max(
                    $this->bitCount,
                    ($index * self::INT_SIZE) + $this->highestBitPosition($value) + 1
                );
            }
        }
    }    

    // Checks if a bit exists
    public function offsetExists(mixed $offset): bool {
        if ($offset < 0) {
            return false; // Negative indices are not valid
        }
        $index = intdiv($offset, self::INT_SIZE);
        return isset($this->data[$index]);
    }

    // Retrieves the value of a bit
    public function offsetGet(mixed $offset): int {
        if ($offset < 0) {
            throw new \OutOfRangeException("Index cannot be negative.");
        }
        $index = intdiv($offset, self::INT_SIZE);
        $bitOffset = $offset % self::INT_SIZE;

        // If the bit does not exist, it defaults to 0
        if (!isset($this->data[$index])) {
            return 0;
        }

        return ($this->data[$index] & (1 << $bitOffset)) !== 0 ? 1 : 0;
    }

    // Sets the value of a bit
    public function offsetSet(mixed $offset, mixed $value): void {
        if ($offset < 0) {
            throw new \OutOfRangeException("Index cannot be negative.");
        }
        if ($value !== 0 && $value !== 1 && $value !== true && $value !== false) {
            throw new \InvalidArgumentException("Bits must be 0, 1, true, or false.");
        }

        $index = intdiv($offset, self::INT_SIZE);
        $bitOffset = $offset % self::INT_SIZE;

        // Initialize the corresponding integer if necessary
        if (!isset($this->data[$index])) {
            $this->data[$index] = 0;
        }

        // Updates the bit
        if ($value === 1) {
            $this->data[$index] |= (1 << $bitOffset); // Sets the bit
        } else {
            $this->data[$index] &= ~(1 << $bitOffset); // Clears the bit
        }

        // Update bitCount if necessary
        $this->bitCount = max($this->bitCount, $offset + 1);
    }

    // Deletes a bit (sets it to 0)
    public function offsetUnset(mixed $offset): void {
        if ($offset < 0) {
            throw new \OutOfRangeException("Index cannot be negative.");
        }
        $index = intdiv($offset, self::INT_SIZE);
        $bitOffset = $offset % self::INT_SIZE;

        if (isset($this->data[$index])) {
            $this->data[$index] &= ~(1 << $bitOffset); // Clears the bit
        }
    }

    public function count(): int {
        $highestBit = 0; // Tracks the highest bit set

        foreach ($this->data as $index => $value) {
            if ($value !== 0) {
                // Find the position of the highest set bit in this chunk
                $highestInChunk = ($index * self::INT_SIZE) + $this->highestBitPosition($value);
                $highestBit = max($highestBit, $highestInChunk);
            }
        }

        // Return the number of bits up to the highest set bit
        return $highestBit + 1; // +1 because bit positions are zero-based
    }

    // Returns an iterator for the BitArray
    public function getIterator(): \Iterator {
        return new BitArrayIterator($this);
    }

    public function __toString(): string {
        // Initialize an empty string for the binary representation
        $binaryString = "";
    
        // Iterate through all bits in reverse order (highest bit to lowest)
        for ($i = $this->count() - 1; $i >= 0; $i--) {
            $binaryString .= $this[$i]; // Append each bit to the string
        }
    
        // Format the result with the "0b" prefix to indicate binary format
        return "0b" . $binaryString;
    }    

    private function addBit(int $bit): void {
        $index = intdiv($this->bitCount, self::INT_SIZE); // Determine which "chunk" to store the bit in
        $offset = $this->bitCount % self::INT_SIZE;      // Determine the bit's position within the chunk

        if (!isset($this->data[$index])) {
            $this->data[$index] = 0; // Initialize the chunk if not set
        }

        if ($bit === 1) {
            $this->data[$index] |= (1 << $offset); // Set the bit
        }

        $this->bitCount++; // Increment the total bit count
    }

    // Helper method to find the position of the highest set bit in an integer
    private function highestBitPosition(int $value): int {
        $position = 0;
        while ($value > 0) {
            $value >>= 1; // Shift right to drop the least significant bit
            $position++;
        }
        return $position - 1; // -1 because position starts from 0
    }
}
?>
