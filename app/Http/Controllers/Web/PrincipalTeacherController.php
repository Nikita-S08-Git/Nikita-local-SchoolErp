<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrincipalTeacherController extends Controller
{
    public function index()
    {
        return view('principal.teachers.index');
    }

    public function create()
    {
        return view('principal.teachers.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('principal.teachers.index');
    }

    public function show($id)
    {
        return view('principal.teachers.show', compact('id'));
    }

    public function edit($id)
    {
        return view('principal.teachers.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('principal.teachers.index');
    }

    public function destroy($id)
    {
        return redirect()->route('principal.teachers.index');
    }
}
