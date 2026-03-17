<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrincipalStudentController extends Controller
{
    public function index()
    {
        // Display list of students for principal
        return view('principal.students.index');
    }

    public function create()
    {
        return view('principal.students.create');
    }

    public function store(Request $request)
    {
        // handle student creation
        return redirect()->route('principal.students.index');
    }

    public function show($id)
    {
        return view('principal.students.show', compact('id'));
    }

    public function edit($id)
    {
        return view('principal.students.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // update student
        return redirect()->route('principal.students.index');
    }

    public function destroy($id)
    {
        // delete student
        return redirect()->route('principal.students.index');
    }
}
