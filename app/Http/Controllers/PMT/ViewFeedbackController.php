<?php

namespace App\Http\Controllers\PMT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewFeedbackController extends Controller
{
    public function index()
    {
        $data['title'] = 'Employee View Feedback';
        return view('employee.view-feedback.index',$data);
    }
}
