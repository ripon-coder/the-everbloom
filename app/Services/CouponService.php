<?php
namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\CouponRepository;

class CouponService
{
    protected $couponRepository;

    public function __construct(CouponRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    public function store(array $data)
    {
        try {
            DB::beginTransaction();

            // Generate unique coupon code if not provided
            if (empty($data['code'])) {
                $code = Str::upper(Str::random(8));
                $originalCode = $code;
                $counter = 1;
                while (Coupon::where('code', $code)->exists()) {
                    $code = $originalCode . $counter;
                    $counter++;
                }
                $data['code'] = $code;
            } else {
                $data['code'] = Str::upper($data['code']);
            }

            $coupon = $this->couponRepository->store($data);

            DB::commit();
            return redirect()->route('admin.coupons.index')
                ->with('success', 'Coupon created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('danger', 'Error creating coupon: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            // Ensure coupon code is uppercase
            if (isset($data['code'])) {
                $data['code'] = Str::upper($data['code']);
            }

            $coupon = $this->couponRepository->update($id, $data);

            DB::commit();

            return redirect()->route('admin.coupons.index')
                ->with('success', 'Coupon updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating coupon: ' . $e->getMessage());
        }
    }

    public function show($coupon)
    {
        return $coupon;
    }

    public function destroy(int $id)
    {
        try {
            $this->couponRepository->destroy($id);

            return redirect()->route('admin.coupons.index')
                ->with('success', 'Coupon deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting coupon: ' . $e->getMessage());
        }
    }

    public function restore(int $id)
    {
        try {
            $this->couponRepository->restore($id);

            return redirect()->route('admin.coupons.index')
                ->with('success', 'Coupon restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error restoring coupon: ' . $e->getMessage());
        }
    }

    public function forceDelete(int $id)
    {
        try {
            $this->couponRepository->forceDelete($id);

            return redirect()->route('admin.coupons.index')
                ->with('success', 'Coupon permanently deleted.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error permanently deleting coupon: ' . $e->getMessage());
        }
    }
}
