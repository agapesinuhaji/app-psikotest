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

        // ❗ Pastikan hanya PARTICIPANT (client)
        if ($user->role !== 'participant') {

            if ($user->role === 'administrator') {
                return redirect('/admin');
            }

        }

        // ===============================
        // DASHBOARD PARTICIPANT
        // ===============================

        $clientTest = ClientTest::where('user_id', $user->id)->first();

        $spm = TypeQuestion::where('status', 'active')
            ->where('slug', 'spm')
            ->get();

        $papiKostick = TypeQuestion::where('status', 'active')
            ->where('slug', 'papi-kostick')
            ->get();

        return view('dashboard', compact('clientTest', 'spm', 'papiKostick'));
    }
}