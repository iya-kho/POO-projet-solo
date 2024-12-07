<?php declare(strict_types=1);

namespace Solo312;

class BitArrayIterator implements \Iterator {
    private BitArray $bitArray;
    private int $position = 0;

    public function __construct(BitArray $bitArray) {
        $this->bitArray = $bitArray;
    }

    public function current(): int {
        return $this->bitArray[$this->position];
    }

    public function key(): int {
        return $this->position;
    }

    public function next(): void {
        $this->position++;
    }

    public function rewind(): void {
        $this->position = 0;
    }

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
            if ($bit !== 0 && $bit !== 1 && $bit !== true && $bit !== false) {
                throw new \InvalidArgumentException("Bits must be 0, 1, true, or false.");
            }
            $bit = (int) $bit;

            $this->addBit($bit);
        }
    }

    public static function fromInt(int $number): self {
        $binary = decbin($number); 
        return self::fromString($binary);
    }

    public static function fromString(string $binary): self {
        $bitArray = new self();
        $binary = ltrim($binary, "0b"); 
        $length = strlen($binary);

        for ($i = 0; $i < $length; $i++) {
            $bitArray[$i] = (int) $binary[$length - $i - 1];
        }

        return $bitArray;
    }

    public function slice(int $offset, ?int $length = null): self {
        $slice = new self(); 
    
        if ($offset < 0) {
            $offset = max(0, $this->count() + $offset);
        } elseif ($offset >= $this->count()) {
            return $slice; 
        }
 
        $end = $length === null
            ? $this->count()
            : ($length < 0
                ? max(0, $this->count() + $length)
                : min($this->count(), $offset + $length)); 
   
        for ($i = $offset; $i < $end; $i++) {
            $slice[$i - $offset] = $this[$i]; 
        }
    
        return $slice; 
    }    

    public function set(array $bits, int $offset = 0): void {
        foreach ($bits as $index => $bit) {
            if ($bit !== 0 && $bit !== 1) {
                throw new \InvalidArgumentException("Only 0 or 1 are allowed.");
            }
            $this[$offset + $index] = $bit;
        }
    }    

    public function unset(int $offset, int $size): void {
        if ($size === 0) {
            return;
        }
    
        $end = $size > 0 
            ? $offset + $size
            : $this->count();
    
        $offset = max(0, $offset);
        $end = max(0, $end);
    
        for ($i = $offset; $i < $end; $i++) {
            if ($i < $this->count()) {
                $this[$i] = 0; 
            }
        }

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

    public function offsetExists(mixed $offset): bool {
        if ($offset < 0) {
            return false;
        }
        $index = intdiv($offset, self::INT_SIZE);
        return isset($this->data[$index]);
    }

    public function offsetGet(mixed $offset): int {
        if ($offset < 0) {
            throw new \OutOfRangeException("Index cannot be negative.");
        }
        $index = intdiv($offset, self::INT_SIZE);
        $bitOffset = $offset % self::INT_SIZE;

        if (!isset($this->data[$index])) {
            return 0;
        }

        return ($this->data[$index] & (1 << $bitOffset)) !== 0 ? 1 : 0;
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        if ($offset < 0) {
            throw new \OutOfRangeException("Index cannot be negative.");
        }
        if ($value !== 0 && $value !== 1 && $value !== true && $value !== false) {
            throw new \InvalidArgumentException("Bits must be 0, 1, true, or false.");
        }

        $index = intdiv($offset, self::INT_SIZE);
        $bitOffset = $offset % self::INT_SIZE;

        if (!isset($this->data[$index])) {
            $this->data[$index] = 0;
        }

        if ($value === 1) {
            $this->data[$index] |= (1 << $bitOffset); 
        } else {
            $this->data[$index] &= ~(1 << $bitOffset);
        }

        $this->bitCount = max($this->bitCount, $offset + 1);
    }

    public function offsetUnset(mixed $offset): void {
        if ($offset < 0) {
            throw new \OutOfRangeException("Index cannot be negative.");
        }
        $index = intdiv($offset, self::INT_SIZE);
        $bitOffset = $offset % self::INT_SIZE;

        if (isset($this->data[$index])) {
            $this->data[$index] &= ~(1 << $bitOffset); 
        }
    }

    public function count(): int {
        $highestBit = 0; 

        foreach ($this->data as $index => $value) {
            if ($value !== 0) {
                $highestInChunk = ($index * self::INT_SIZE) + $this->highestBitPosition($value);
                $highestBit = max($highestBit, $highestInChunk);
            }
        }

        return $highestBit + 1; // +1 because bit positions are zero-based
    }

    public function getIterator(): \Iterator {
        return new BitArrayIterator($this);
    }

    public function __toString(): string {
        $binaryString = "";
    
        for ($i = $this->count() - 1; $i >= 0; $i--) {
            $binaryString .= $this[$i];
        }
    
        return "0b" . $binaryString;
    }    

    private function addBit(int $bit): void {
        $index = intdiv($this->bitCount, self::INT_SIZE);
        $offset = $this->bitCount % self::INT_SIZE;

        if (!isset($this->data[$index])) {
            $this->data[$index] = 0; 
        }

        if ($bit === 1) {
            $this->data[$index] |= (1 << $offset);
        }

        $this->bitCount++;
    }

    private function highestBitPosition(int $value): int {
        $position = 0;
        while ($value > 0) {
            $value >>= 1; 
            $position++;
        }
        return $position - 1; 
    }
}
?>
