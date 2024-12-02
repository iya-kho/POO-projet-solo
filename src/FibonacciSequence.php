<?php declare(strict_types=1);

namespace Solo312;

class FibonacciSequence implements \Iterator {
    private ?int $max; //Number of Fibonacci sequence numbers to generate
    private int $currentIndex = 0; //Current index of the sequence
    private int $currentValue = 0; //Current value of the sequence
    private int $previousValue = 1; //Previous value of the sequence, set to 1 by default (the second Fibonacchi number)
    private static array $cache = [0, 1]; //Stock calculated values

    public function __construct(?int $max = null, int $start = 0) {
        $this->max=$max;
        $this->currentIndex = $start;
    }

    public static function first(int $n): self {
        // Create an iterator starting from F(0) and limited to $n terms
        return new self($n);
    }

    public static function range(int $start, ?int $length = -1 ): self {
        // Create an iterator starting at $start with $length terms
        return $length > 0 ? new self($start + $length, $start) : new self(null, $start);
    }

    //Calculate and return the current value
    // Return the current Fibonacci value
    public function current(): mixed {
        return $this->fibonacci($this->currentIndex);
    }

    //Return the current index
    public function key(): mixed {
        return $this->currentIndex;
    }

    //Move to the next index
    public function next(): void {
        $this->currentIndex++;
    }

     // Reset the iterator to the beginning
    public function rewind(): void {
        $this->currentValue = $this->fibonacci($this->currentIndex); 
        $this->previousValue = $this->currentIndex > 0 ? $this->fibonacci($this->currentIndex - 1) : 0;
    }

    // Check if the current index is valid and must be iterated
    public function valid(): bool {
       // Valid if there's no max limit or the current index is less than the max
       return $this->max === null || $this->currentIndex < $this->max;
    }

    private function fibonacci(int $n): int {
        // Use cached value if it exists
        if (isset(self::$cache[$n])) {
            return self::$cache[$n];
        }
    
        // Calculate and cache the result
        self::$cache[$n] = $this->fibonacci($n - 1) + $this->fibonacci($n - 2);
        return self::$cache[$n];
    }
}
?>
