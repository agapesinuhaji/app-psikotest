<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClientTest;
use App\Models\TypeQuestion;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 🔁 Redirect berdasarkan role
        if ($user->role === 'client') {
            return redirect('/client');
        }

        if ($user->role === 'administrator') {
            return redirect('/admin');
        }

        // ===============================
        // Default dashboard (jika bukan 2 role di atas)
        // ===============================

        $clientTest = ClientTest::where('user_id', $user->id)->first();

        $spm = TypeQuestion::where('status', 'active')
            ->where('slug', 'spm')
            ->first();

        $papiKostick = TypeQuestion::where('status', 'active')
            ->where('slug', 'papi-kostick')
            ->first();

        return view('dashboard', compact('clientTest', 'spm', 'papiKostick'));
    }
}