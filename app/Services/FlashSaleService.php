<?php
namespace App\Services;

use App\Models\FlashSale;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\FlashSaleRepository;

class FlashSaleService
{
    protected $flashSaleRepository;

    public function __construct(FlashSaleRepository $flashSaleRepository)
    {
        $this->flashSaleRepository = $flashSaleRepository;
    }

    public function store(array $data)
    {
        try {
            DB::beginTransaction();

            // Generate unique slug if not provided
            if (empty($data['slug'])) {
                $slug = Str::slug($data['name']);
                $originalSlug = $slug;
                $counter = 1;
                while (FlashSale::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $data['slug'] = $slug;
            } else {
                $data['slug'] = Str::slug($data['slug']);
            }

            $flashSale = $this->flashSaleRepository->store($data);

            DB::commit();
            return redirect()->route('admin.flash-sales.index')
                ->with('success', 'Flash Sale created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('danger', 'Error creating Flash Sale: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            // Ensure slug is a slug
            if (isset($data['slug'])) {
                $data['slug'] = Str::slug($data['slug']);
            }
            
            // If name is updated and slug is not provided, regenerate slug from name
            if (isset($data['name']) && !isset($data['slug'])) {
                $slug = Str::slug($data['name']);
                $originalSlug = $slug;
                $counter = 1;
                while (FlashSale::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $data['slug'] = $slug;
            }

            $flashSale = $this->flashSaleRepository->update($id, $data);

            DB::commit();

            return redirect()->route('admin.flash-sales.index')
                ->with('success', 'Flash Sale updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating Flash Sale: ' . $e->getMessage());
        }
    }

    public function show($flashSale)
    {
        return $flashSale;
    }

    public function destroy(int $id)
    {
        try {
            $this->flashSaleRepository->destroy($id);

            return redirect()->route('admin.flash-sales.index')
                ->with('success', 'Flash Sale deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting Flash Sale: ' . $e->getMessage());
        }
    }

    public function restore(int $id)
    {
        try {
            $this->flashSaleRepository->restore($id);

            return redirect()->route('admin.flash-sales.index')
                ->with('success', 'Flash Sale restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error restoring Flash Sale: ' . $e->getMessage());
        }
    }

    public function forceDelete(int $id)
    {
        try {
            $this->flashSaleRepository->forceDelete($id);

            return redirect()->route('admin.flash-sales.index')
                ->with('success', 'Flash Sale permanently deleted.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error permanently deleting Flash Sale: ' . $e->getMessage());
        }
    }
}
