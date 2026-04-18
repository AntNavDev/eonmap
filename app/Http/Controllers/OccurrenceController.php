<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class OccurrenceController extends Controller
{
    public function show(int $id): View
    {
        return view('occurrences.show', ['id' => $id]);
    }
}
