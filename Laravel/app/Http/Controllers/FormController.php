<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Country;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::with('country')->get();
        return view('forms.index', compact('forms'));
    }

    public function create()
    {
        $countries = Country::all();
        return view('forms.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        Form::create($request->all());
        return redirect()->route('forms.index')->with('success', 'Form created successfully.');
    }

    public function edit($id)
    {
        $form = Form::with('fields')->findOrFail($id);
        $countries = Country::all();
        return view('forms.edit', compact('form', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $form = Form::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $form->update($request->all());
        return redirect()->route('forms.index')->with('success', 'Form updated successfully.');
    }

    public function destroy($id)
    {
        $form = Form::findOrFail($id);
        $form->delete();
        return redirect()->route('forms.index')->with('success', 'Form deleted successfully.');
    }
}
