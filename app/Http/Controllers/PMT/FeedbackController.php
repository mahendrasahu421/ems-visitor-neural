<?php

namespace App\Http\Controllers\PMT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $data['title'] = 'Feedback';
        return view('employee.feedback.index',$data);
    }
}
