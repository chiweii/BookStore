<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type_id' => 'nullable',
            'ISBN' => 'required',
            'name' => 'required|string',
            'description' => 'required',
            'publisher_id' => 'required|numeric',
            'publish_date' => 'required|date',
            'author_id' => 'required|numeric',
            'book_classification' => 'required',
        ];
    }
}
