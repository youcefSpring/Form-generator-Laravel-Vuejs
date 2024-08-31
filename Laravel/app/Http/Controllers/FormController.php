<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormField;

class FormController extends Controller
{
    public function index()
    {
        $countries = Country::whereDoesntHave('form')->withCount('form')->get();
        $forms = Form::with('country', 'fields')->get();

        // foreach ( $forms as $form) {
        //     foreach ($form->fields as $f){
        //         if($f->type == "dropdown"){
        //             $f->options=json_decode($f->options);
        //         }
        //     }

        // };
        return response()->json(
            [
                   'forms' => $forms,
                   'countries' => $countries
                ], 200);
    }
    // Store a new form
    public function store(Request $request)
    {
        $input=$request->all();

        $validatedData = $request->validate([
            'country_id' => 'required'
        ]);

        // Create the new form
        $form = Form::create([
            'country_id' => $validatedData['country_id']
        ]);

        // Create the associated fields
        foreach ($request->fields as $fieldData) {
            FormField::create([
                'form_id' => $form->id,
                'type' => $fieldData['type'],
                'category' => $fieldData['category'],
                'is_required' => $fieldData['is_required'] == true ? 1 : 0,
                'options' => isset($fieldData['options']) ? json_encode($fieldData['options']) : null,
            ]);
        }

        return response()->json($form->with('fields','country'), 201);
    }

    // Update an existing form
    public function update(Request $request, $id)
    {
          $form=Form::findOrFail($id);

          if(!isset($form)){
            return response()->json([], 400);
          }else{
            $form->country_id=$request->country_id;
            $form->fields()->delete();
            foreach ($request->form['fields'] as $fieldData) {
                FormField::create([
                    'form_id' => $form->id,
                    'type' => $fieldData['type'],
                    'category' => $fieldData['category'],
                    'is_required' => $fieldData['is_required'] == true ? 1 : 0,
                    'options' => json_encode($fieldData['options']),
                ]);
            }
            return response()->json($form->with('fields','country'), 201);
          }
        // $validatedData = $request->validate([
        //     'country_id' => 'required|exists:countries,id',
        //     'fields' => 'required|array',
        //     'fields.*.type' => 'required|string',
        //     'fields.*.category' => 'required|string',
        //     'fields.*.is_required' => 'required|boolean',
        //     'fields.*.options' => 'nullable|array',
        // ]);

        // // Update form's country
        // $form->update([
        //     'country_id' => $validatedData['country_id'],
        // ]);

        // // Delete old fields
        // $form->fields()->delete();

        // // Add updated fields
        // foreach ($validatedData['fields'] as $fieldData) {
        //     $form->fields()->create([
        //         'type' => $fieldData['type'],
        //         'category' => $fieldData['category'],
        //         'is_required' => $fieldData['is_required'],
        //         'options' => json_encode($fieldData['options']),
        //     ]);
        // }

        return response()->json($form->load('fields'), 200);
    }

    // Delete a form
    public function destroy($id)
    {
        $form=Form::where('id',$id)->first();
        $form->fields()->delete(); // Delete associated fields first
        $form->delete(); // Delete the form

        return response()->json(['message' => 'Form deleted successfully'], 200);
    }

    // Show a form for a specific country
    public function show($id)
    {
        $form = Form::where('id', $id)->with('fields')->first();

        if (!$form) {
            return response()->json(['message' => 'Form not found'], 404);
        }

        return response()->json($form, 200);
    }
}
