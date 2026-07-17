<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $teacher = request()->user()?->teacher()->with(['subjects', 'user'])->first();

        return view('teacher.profile', [
            'teacher' => $teacher,
        ]);
    }
}
