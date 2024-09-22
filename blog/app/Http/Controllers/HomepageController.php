<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function homePage()
    {
        return view('homePage');
    }
}
