<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $productId = null,
        public readonly ?int $variantId = null,
        public readonly int $available = 0,
        public readonly int $requested = 0,
    ) {
        parent::__construct($message);
    }
}

