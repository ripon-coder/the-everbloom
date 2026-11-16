<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\District;
use App\Models\Thana;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function districts(): JsonResponse
    {
        return response()->json([
            'districts' => District::active()->get(['id', 'name', 'bn_name', 'delivery_charge']),
        ]);
    }

    public function thanas(District $district): JsonResponse
    {
        if (!$district->is_active) {
            return response()->json(['thanas' => []]);
        }

        return response()->json([
            'thanas' => Thana::active()
                ->where('district_id', $district->id)
                ->get(['id', 'name', 'bn_name']),
        ]);
    }

    public function areas(Thana $thana): JsonResponse
    {
        if (!$thana->is_active) {
            return response()->json(['areas' => []]);
        }

        return response()->json([
            'areas' => Area::active()
                ->where('thana_id', $thana->id)
                ->get(['id', 'name', 'bn_name', 'postal_code']),
        ]);
    }
}

