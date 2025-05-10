<?php

namespace App;

enum VariantEnum: string
{
    case ACTIVE = "active";
    case INACTIVE = "inactive";

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-success',
            self::INACTIVE => 'bg-danger',
        };
    }
}
