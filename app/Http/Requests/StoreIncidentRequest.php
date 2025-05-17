<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
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
            'description'           => 'required|string|max:2555',
            'report_id'             => 'nullable|exists:reports,id',
            'location_id'           => 'nullable|exists:locations,id',
            'incident_type_id'      => 'required|exists:incident_types,id',
            'incident_status_id'    => 'nullable|exists:incident_statuses,id',
            'files'                 => 'required|array|min:1|max:5',
            'files.*'               => 'file|mimes:jpg,jpeg,png|max:10240',

            // reportador
            'names'                 => 'required|string|max:255',
            'first_surname'         => 'nullable|string|max:255',
            'second_surname'        => 'nullable|string|max:255',
            'number'                => 'required|string|max:255',
            'contact_email'         => 'required|email|max:255',

            // location
            'street'                => 'required|string|max:255',
            'interior_number'       => 'required|string|max:255',
            'exterior_number'       => 'required|string|max:255',
            'additional'            => 'required|string|max:2555',
            'references'            => 'required|string|max:2555',
            'neighborhood_id'       => 'required|exists:neighborhoods,id',
            'lat'                   => 'required|numeric',
            'lng'                   => 'required|numeric',
        ];
    }
    public function attributes(): array
    {
        return [
            'description'           => 'descripción',
            'report_id'             => 'solicitante',
            'location_id'           => 'ubicación',
            'incident_type_id'      => 'tipo de incidencia',
            'incident_status_id'    => 'estatus de incidencia',
            'files'                 => 'imagen',
            'files.*'               => 'imagenes',
            'names'                 => 'nombre(s)',
            'first_surname'         => 'apellido paterno',
            'second_surname'        => 'apellido materno',
            'number'                => 'número de teléfono',
            'contact_email'         => 'correo electronico',
            'street'                => 'calle',
            'interior_number'       => 'número interior',
            'exterior_number'       => 'número exterior',
            'additional'            => 'adicional',
            'references'            => 'referencias',
            'neighborhood_id'       => 'colonia',
            'lat'                   => 'latitud',
            'lng'                   => 'longitud',
        ];
    }
}
