<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentAuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'enrollment_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $student = Student::where('enrollment_number', $credentials['enrollment_number'])->first();

        if (! $student || ! Hash::check($credentials['password'], $student->password)) {
            return back()->withErrors([
                'enrollment_number' => 'The enrollment number or password is incorrect.',
            ])->onlyInput('enrollment_number');
        }

        $request->session()->regenerate();
        $request->session()->put('student_id', $student->id);

        return redirect()->route('student.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('student_id');
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
