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

        $typeQuestions = \App\Models\TypeQuestion::where('status', 'active')->get();


        return view('dashboard', compact('clientTest', 'typeQuestions'));
    }
}
