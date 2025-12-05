<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientTest;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data client_test milik user yang sedang login
        $clientTest = ClientTest::where('user_id', auth()->id())->first();

        $spm = \App\Models\TypeQuestion::where('status', 'active')
                ->where('slug', 'spm')
                ->get();


        $papiKostick = \App\Models\TypeQuestion::where('status', 'active')
            ->where('slug', 'papi-kostick')
            ->get();

     


        return view('dashboard', compact('clientTest', 'spm', 'papiKostick'));
    }
}
