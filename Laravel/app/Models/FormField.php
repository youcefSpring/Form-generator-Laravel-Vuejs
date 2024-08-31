<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = ['form_id', 'name', 'type', 'category', 'options', 'is_required'];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($formField) {
            if ($formField->type === 'dropdown' && is_string($formField->options)) {
                $formField->options = json_decode($formField->options, true);
            }
        });
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }



}
