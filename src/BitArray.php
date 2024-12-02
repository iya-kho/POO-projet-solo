<?php declare(strict_types=1);

class BitArrayIterator implements Iterator {
    public function __construct() {}
    public function current(): int {}
    public function key(): int {}
    public function next(): void {}
    public function rewind(): void {}
    public function valid(): bool {}
}

class BitArray implements ArrayAccess, Countable, IteratorAggregate, Stringable {
    private const BYTE_SIZE = 8;
    private const INT_SIZE = PHP_INT_SIZE * self::BYTE_SIZE;

    public function __construct() {}

    public static function fromInt(int $from) {}

    public static function fromString(string $from) {}

    public function slice(int $start = 0, int $length = -1): self {}

    public function set(array $bits, int $start = 0): void {}

    public function unset(int $start, int $length = -1): void {}

    public function offsetExists(mixed $offset): bool {}

    public function offsetGet(mixed $offset): int {}

    public function offsetSet(mixed $offset, mixed $value): void {}

    public function offsetUnset(mixed $offset): void {}

    public function count(): int {}

    public function getIterator(): BitArrayIterator {}

    public function __toString(): string {}
}
?>
