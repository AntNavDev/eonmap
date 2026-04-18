<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class TaxonController extends Controller
{
    public function show(string $name): View
    {
        return view('taxa.show', ['name' => $name]);
    }
}
