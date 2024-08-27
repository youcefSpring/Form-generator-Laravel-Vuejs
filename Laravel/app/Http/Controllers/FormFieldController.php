<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\Request;

class FormFieldController extends Controller
{
    public function create($formId)
    {
        $form = Form::findOrFail($formId);
        return view('fields.create', compact('form'));
    }

    public function store(Request $request, $formId)
    {
        $form = Form::findOrFail($formId);

        $request->validate([
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'category' => 'required|string|max:50',
            'options' => 'nullable|array',
            'is_required' => 'required|boolean',
        ]);

        $form->fields()->create($request->all());
        return redirect()->route('forms.edit', $form->id)->with('success', 'Field added successfully.');
    }

    public function edit($formId, $fieldId)
    {
        $form = Form::findOrFail($formId);
        $field = FormField::findOrFail($fieldId);
        return view('fields.edit', compact('form', 'field'));
    }

    public function update(Request $request, $formId, $fieldId)
    {
        $form = Form::findOrFail($formId);
        $field = FormField::findOrFail($fieldId);

        $request->validate([
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'category' => 'required|string|max:50',
            'options' => 'nullable|array',
            'is_required' => 'required|boolean',
        ]);

        $field->update($request->all());
        return redirect()->route('forms.edit', $form->id)->with('success', 'Field updated successfully.');
    }

    public function destroy($formId, $fieldId)
    {
        $form = Form::findOrFail($formId);
        $field = FormField::findOrFail($fieldId);
        $field->delete();
        return redirect()->route('forms.edit', $form->id)->with('success', 'Field deleted successfully.');
    }
}
