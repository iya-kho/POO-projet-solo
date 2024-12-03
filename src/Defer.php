<?php declare(strict_types=1);

namespace Solo312;

class Callback {
    public function __construct(
        private mixed $cb,
        private array $args = [],
    ) {}

    public function call(): void {
        call_user_func_array($this->cb, $this->args);
    }
}

class Defer {
    private array $callables = [];

    public static function init(): self {
        // Retourne une nouvelle instance de Defer
        return new self();
    }

    public function __invoke(callable $cb, array $args = []): void {
        // Ajouter le callable à la pile
        $this->callables[] = new Callback($cb, $args);;
    }
    
    public function __destruct() {
        while (!empty($this->callables)) {
            $callable = array_pop($this->callables); // Take the last callable
            $callable->call(); // Execute
        }
    }
}
?>
