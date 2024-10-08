<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Util\Util;
use Closure;

class MediaFromRequest extends FormRequest
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

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json(['status'=>'failed',
                                                    'message'=>$validator->errors()->first(),
                                                    'errors'=>$validator->errors()->all()], 422));
    }


    public function rules(){
        $rules  = [];
        $rules['post_files'] = ['required', function (string $attribute, mixed $value, Closure $fail) {
            $subviewImageFileArr = request()->file('post_files');
            $imageFound = [];
            foreach($subviewImageFileArr as $file){
                if($file){
                    $fileNameWithExt = $file->getClientOriginalName();
                    $fileExist = Util::isFileExists($fileNameWithExt);
                    if($fileExist){
                        $fail('Image ['.$fileNameWithExt.'] File already exists');
                    }

                }
            }
        },];


        return $rules;
    }
}
