<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    public function create($formId)
    {
        $form = Form::with('fields')->findOrFail($formId);
        return view('submissions.create', compact('form'));
    }

    public function store(Request $request, $formId)
    {
        $form = Form::with('fields')->findOrFail($formId);

        $submissionData = $request->validate(
            collect($form->fields)->mapWithKeys(function ($field) {
                return [$field->name => $field->is_required ? 'required' : 'nullable'];
            })->toArray()
        );

        $form->submissions()->create([
            'user_id' => auth()->id(),
            'submission_data' => $submissionData,
        ]);

        return redirect()->route('forms.index')->with('success', 'Form submitted successfully.');
    }

    public function show($formId, $submissionId)
    {
        $submission = FormSubmission::with('form')->findOrFail($submissionId);
        return view('submissions.show', compact('submission'));
    }
}
