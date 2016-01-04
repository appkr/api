<?php

namespace Appkr\Api\Example;

use Appkr\Api\Http\Request;

class BooksRequest extends Request
{
    /**
     * @var array
     */
    protected $rules = [
        'title'       => 'required|min:2',
        'description' => 'min:2',
    ];

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
        $rules = $this->rules;

        if (is_update_request()) {
            $rules['out_of_print'] = 'boolean';
        }

        if (is_delete_request()) {
            $rules = [];
        }

        return $rules;
    }
}