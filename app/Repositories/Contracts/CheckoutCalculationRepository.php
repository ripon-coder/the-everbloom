<?php

namespace App\Repositories\Contracts;

interface CheckoutCalculationRepository
{
    /**
     * Calculate and validate checkout data based on cart items.
     *
     * @param array $cartItems
     * @param string|null $districtId
     * @param string|null $couponCode
     * @return array
     */
    public function calculate(array $cartItems, ?string $districtId = null, ?string $couponCode = null): array;
}
