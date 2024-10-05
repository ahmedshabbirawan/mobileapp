<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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

        $rules['type'] = 'required';

        /*

        if(!request()->get('id')){
            $rules['template_thumbnail_input'] = 'required';
        }


        $types = request()->get('type');
        $index = 0;
        if(is_array($types) && count($types) > 0){
            foreach($types as $type){
                if($type == 'Image'){
                    $rules['subview_image_file'] = 'required'; 
                }
                $index++;
            }
        }

        */
        


        
        return $rules;
    }



    public function messages()
    {
    return [
      'name.required' => 'Template Title is required',
    ];
    }




}
