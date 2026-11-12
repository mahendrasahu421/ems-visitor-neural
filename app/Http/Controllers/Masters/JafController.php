<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JafController extends Controller
{
    public function index()
    {
        $data['title'] = 'Create Job Questionnaire';
        return view('home.jobs.jaf', $data);
    }
}
