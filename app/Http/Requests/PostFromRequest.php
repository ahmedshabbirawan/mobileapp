<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Storage;
use App\Util\Util;
use Closure;

class PostFromRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        $rules  = [];
        
        $rules['name'] = 'required';
        $rules['category'] = 'required';
        $rules['frame'] = 'required';
        $rules['template_bounds'] = 'required';

        $rules['template_thumbnail_input'] = ['required', function (string $attribute, mixed $value, Closure $fail) {
            $thumbnailImageFile = request()->file('template_thumbnail_input');
            $fileNameWithExt = $thumbnailImageFile->getClientOriginalName();
            $fileExist = Util::isFileExists($fileNameWithExt);
            if($fileExist){
                $fail('In subviews ['.$fileNameWithExt.'] File already exists');
            }
        },];


        $rules['type'] = 'required';
        $rules['subview_image_file'] = [function (string $attribute, mixed $value, Closure $fail) {
            // if ($value === 'foo') {
            //     $fail("The {$attribute} is invalid.");
            // }
            $types = request()->get('type');
            $subviewImageFileArr = request()->file('subview_image_file');
            $index = 0;
            $imageFound = [];
            foreach($types as $type){
                if(isset($subviewImageFileArr[$index])){
                    $subViewImage = null;
                    $fileNameWithExt = $subviewImageFileArr[$index]->getClientOriginalName();
                    $fileExist = Util::isFileExists($fileNameWithExt);
                    if($fileExist){
                        $fail('Thumbnail ['.$fileNameWithExt.'] File already exists');
                    }

                }
                $index++;
            }
        },];
        return $rules;
    }


    protected function passedValidation_____________(){
        $types = request()->get('type');
        $subviewImageFileArr = request()->file('subview_image_file');
        $index = 0;
        $imageFound = [];
        foreach($types as $type){
            if(isset($subviewImageFileArr[$index])){
                $subViewImage = null;
                $fileNameWithExt = $subviewImageFileArr[$index]->getClientOriginalName();
                $fileExist = Util::isFileExists($fileNameWithExt);

                if($fileExist){
                    return response()->json(['message' => $fileNameWithExt.' : File already exists' ], 422);
                }

            }
            $index++;
        }


    }



    public function messages()
    {
    return [
      'name.required' => 'Template Title is required',
    ];
    }




}
