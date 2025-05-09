<?php

namespace App;

enum ProductFeatureEnum: string
{
    case YES = "yes";
    case NO = "no";

    public function label(): string
    {
        return match ($this) {
            self::YES => 'Yes',
            self::NO => 'No',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::YES => 'bg-success',
            self::NO => 'bg-danger',
        };
    }
}
