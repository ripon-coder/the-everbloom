<?php
namespace App\Repositories\Eloquent;

use App\Models\FlashSale;
use App\Repositories\Contracts\FlashSaleRepository;

class FlashSaleEloquent implements FlashSaleRepository
{
    public function index()
    {
        return FlashSale::select('id', 'name', 'slug', 'start_date', 'end_date', 'status', 'created_at')
            ->latest()
            ->paginate(15);
    }

    public function create()
    {
        $data['status_options'] = \App\Constants\FlashSaleStatus::getOptions();
        $data['products'] = \App\Models\Product::active()->get();
        return $data;
    }

    public function store(array $data)
    {
        $flashSale = FlashSale::create($data);
        
        // Sync products if provided
        if (isset($data['products'])) {
            $syncData = [];
            foreach ($data['products'] as $productId) {
                $syncData[$productId] = [
                    'discount_price' => $data['discount_price'] ?? null,
                    'discount_percentage' => $data['discount_percentage'] ?? null,
                ];
            }
            $flashSale->products()->sync($syncData);
        }
        
        return $flashSale;
    }

    public function edit(int $id)
    {
        $data['flashSale'] = FlashSale::findOrFail($id);
        $data['status_options'] = \App\Constants\FlashSaleStatus::getOptions();
        $data['products'] = \App\Models\Product::active()->get();
        return $data;
    }

    public function update(int $id, array $data)
    {
        $flashSale = FlashSale::findOrFail($id);
        $flashSale->update($data);
        
        // Sync products if provided
        if (isset($data['products'])) {
            $syncData = [];
            foreach ($data['products'] as $productId) {
                $syncData[$productId] = [
                    'discount_price' => $data['discount_price'] ?? null,
                    'discount_percentage' => $data['discount_percentage'] ?? null,
                ];
            }
            $flashSale->products()->sync($syncData);
        } else {
            // Remove all products if none selected
            $flashSale->products()->detach();
        }
        
        return $flashSale;
    }

    public function destroy(int $id)
    {
        $flashSale = FlashSale::findOrFail($id);
        return $flashSale->delete();
    }

    public function restore(int $id)
    {
        $flashSale = FlashSale::withTrashed()->findOrFail($id);
        return $flashSale->restore();
    }

    public function forceDelete(int $id)
    {
        $flashSale = FlashSale::withTrashed()->findOrFail($id);
        return $flashSale->forceDelete();
    }
}
