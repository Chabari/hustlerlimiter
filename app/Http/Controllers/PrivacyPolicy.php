<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivacyPolicy extends Controller
{
    //

    public function privacypolicy()
    {
        return view('layouts.policy');
    }
}
