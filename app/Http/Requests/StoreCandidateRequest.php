<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'principal_name' => 'required|string',
            'principal_faculty' => 'required|string',
            'principal_program' => 'required|string',
            'voting_option_id' => 'required|numeric|exists:voting_options,id',
    
            // Suplente (opcionales)
            'substitute_name' => 'nullable|string',
            'substitute_faculty' => 'nullable|string',
            'substitute_program' => 'nullable|string',
    
            // Foto (opcional, tipo imagen, tamaño máximo 2MB)
            'photo' => 'nullable|image|max:2048',
        ];
    }
    
}
