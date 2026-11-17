<?php

namespace App\Http\Requests\Admin;

class UpdateProductRequest extends StoreProductRequest
{
    protected function productId(): int|string|null
    {
        return $this->route('product');
    }
}

