<?php

namespace App\Http\Controllers\PMT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewEmployeeController extends Controller
{
    public function index()
    {
        $data['title'] = 'competencies';
        return view('employee.review-employee.index',$data);
    }
    
}
