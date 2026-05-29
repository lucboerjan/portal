<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivacyPinController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        if ($request->pin !== config('app.privacy_pin')) {
            return response()->json(['success' => false], 401);
        }

        session(['privacy_unlocked' => true]);

        return response()->json(['success' => true]);
    }
}