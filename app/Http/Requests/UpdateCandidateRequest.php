<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'principal_name' => 'required|string',
            'principal_faculty' => 'required|string',
            'principal_program' => 'required|string',
            'voting_option_id' => 'required|numeric',

            'substitute_name' => 'nullable|string',
            'substitute_faculty' => 'nullable|string',
            'substitute_program' => 'nullable|string',

            'photo' => 'nullable|image|max:2048'
        ];
    }
}
