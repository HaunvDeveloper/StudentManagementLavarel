<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;

class AddressAPIController extends Controller
{
    public function provinces()
    {
        $list = Province::select('Code', 'Name')->get();
        return response()->json($list);
    }

    public function districts($id)
    {
        $list = District::where('ProvinceCode', $id)
            ->select('Code', 'Name', 'ProvinceCode')
            ->get();
        return response()->json($list);
    }

    public function wards($id)
    {
        $list = Ward::where('DistrictCode', $id)
            ->select('Code', 'Name', 'DistrictCode')
            ->get();
        return response()->json($list);
    }
}
