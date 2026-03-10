<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class AnswerPdfService
{
    public static function generate(User $user)
    {
        $answers = $user->answers()->with('question')->get();

        $pdf = Pdf::loadView('pdf.answer-sheet', [
            'user' => $user,
            'answers' => $answers,
        ]);

        $path = storage_path("app/temp/answer-{$user->id}.pdf");

        $pdf->save($path);

        return $path;
    }
}