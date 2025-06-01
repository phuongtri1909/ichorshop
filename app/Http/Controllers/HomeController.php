<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller{
    public function index(Request $request)
    {
       return view('client.pages.home', [
           
        ]);
    }
}