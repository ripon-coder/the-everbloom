<?php

namespace App\Repositories\Contracts;

interface CheckoutCalculationRepository
{
    /**
     * Calculate and validate checkout data based on cart items.
     *
     * @param array $cartItems
     * @param string|null $shippingMethod
     * @param string|null $couponCode
     * @return array
     */
    public function calculate(array $cartItems, ?string $shippingMethod = null, ?string $couponCode = null): array;
}
