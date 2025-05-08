<?php

namespace App\Observers;

use App\Models\Brand;
use Illuminate\Support\Str;

class BrandObserver
{
    /**
     * Handle the Brand "created" event.
     */
    public function creating(Brand $brand): void
    {
        $slug = Str::slug($brand->name);
        $total_count = Brand::where("slug", $slug)->count();
        $brand->slug = $total_count > 0 ? "{$slug}-{$total_count}" : $slug;
    }

    public function created(Brand $brand): void
    {
        // $slug = Str::slug($brand->name);
        // $brand->slug = Brand::where("slug",$slug)->exists() ? "{$slug}-{$brand->id}" : $slug;
    }

    /**
     * Handle the Brand "updated" event.
     */
    public function updating(Brand $brand): void
    {
        if ($brand->isDirty('name')) {
            $slug = Str::slug($brand->name);
            $total_count = Brand::where("slug", $slug)->count() + 1;
            $brand->slug = Brand::where("slug", $slug)->exists() ? "{$slug}-{$total_count}" : $slug;
        }
    }
    public function updated(Brand $brand): void
    {
        // if ($brand->isDirty('name')) {
        //     $slug = Str::slug($brand->name);
        //     $brand->slug = Brand::where("slug", $slug)->exists() ? "{$slug}-{$brand->id}" : $slug;
        //     $brand->save();
        // }
    }

    /**
     * Handle the Brand "deleted" event.
     */
    public function deleted(Brand $brand): void
    {
        //
    }

    /**
     * Handle the Brand "restored" event.
     */
    public function restored(Brand $brand): void
    {
        //
    }

    /**
     * Handle the Brand "force deleted" event.
     */
    public function forceDeleted(Brand $brand): void
    {
        //
    }
}
