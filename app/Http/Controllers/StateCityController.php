<?php

namespace App\Http\Controllers;
use App\Models\StateCity;
use Illuminate\Http\Request;

class StateCityController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->get('term', '');

        $districts = StateCity::where('name', 'LIKE', '%' . $term . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        $results = $districts->map(function ($district) {
            return [
                'id' => $district->id,
                'text' => $district->name,
            ];
        });

        return response()->json($results);
    }
}
