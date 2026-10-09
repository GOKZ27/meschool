<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ProgramController extends Controller
{
    /**
     * One program landing page, driven entirely by config('school.program_catalog').
     *
     * Each route in routes/web.php registers a literal URI plus a 'program'
     * route default, so an unknown slug can never sneak in through the URL —
     * the route itself does not exist for it.
     */
    public function show(string $program): View
    {
        $page = config("school.program_catalog.items.{$program}");

        abort_unless(is_array($page), 404);

        return view('program.show', [
            'slug' => $program,
            'page' => $page,
            'all' => config('school.program_catalog.items', []),
        ]);
    }
}
