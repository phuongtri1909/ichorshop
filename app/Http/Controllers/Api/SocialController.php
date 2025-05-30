<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SocialController extends Controller
{
  public function index()
  {
        $socials = Social::get();

        $socials->map(function ($social) {
            $social->icon = asset( $social->icon);
            return $social;
        });

        return response()->json([
            'success' => true,
            'data' => $socials,
        ]);
  }
}