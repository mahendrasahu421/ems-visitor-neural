<?php

namespace App\Http\Controllers;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillsController extends Controller
{
    public function skillsSearch(Request $request)
    {
        $term = $request->get('term', '');

        $districts = Skill::where('name', 'LIKE', '%' . $term . '%')
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
