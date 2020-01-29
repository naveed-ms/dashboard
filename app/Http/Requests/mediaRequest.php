<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class mediaRequest extends Request
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
            "title"=>"required | min:4",
            "mp3_file"=>"required | mimes:mp3",
            "mp4_720_file"=>"mimes:mp4",
            "mp4_360_file"=>"mimes:mp4"
        ];
    }
}
