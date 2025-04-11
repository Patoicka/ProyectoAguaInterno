<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvidenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comments'      => 'nullable|string|max:2555',
            'incident_id'   => 'required|exists:incidents,id',
            'files'         => 'required|array|min:1|max:5',
            'files.*'       => 'file|mimes:jpg,jpeg,png|max:10240',
        ];
    }
    public function attributes(): array
    {
        return [
            'comments'      => 'comentarios',
            'files'         => 'imagenes',
            'files.*'       => 'imagen',
        ];
    }
}
