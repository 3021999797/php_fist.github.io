<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSchoolByEmail extends FormRequest
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
            'id'=>'required|integer|min:0',
            'school_email'=>'required|email',
            'password'=>'required|string|min:8|max:30',
        ];
    }
    public function messages()
    {
        return [
            'id.required'=>'id不能为空',
            'id.integer'=>'id非整形',
            'school_email.required'=>'邮箱不能为空',
            'school_email.email'=>'邮箱格式不正确',
            'password.required'=>'密码不能为空',
            'password.min'=>'密码不能小于8个字符',
            'password.max'=>'密码不能超过3个字符',
        ];
    }
    protected function failedValidation(Validator $validator)
    {

        throw(new HttpResponseException(json_fail('参数错误', $validator->errors()->all(), 422)));
    }
}
