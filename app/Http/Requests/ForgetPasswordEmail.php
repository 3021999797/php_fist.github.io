<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ForgetPasswordEmail extends FormRequest
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
            'account'=>'required|string|min:1|max:50',
            'school_email'=>'required|email',
        ];
    }
    public function messages()
    {
        return [
            'account.required' => '账号不能为空',
            'account.min' => '账号小于1个字符',
            'account.max' => '账号不能超过50个字符',
            'school_email.required' => '邮箱不能为空',
            'school_email.email' => '邮箱格式不正确',
        ];
    }
    protected function failedValidation(Validator $validator)
    {

        throw(new HttpResponseException(json_fail('参数错误', $validator->errors()->all(), 422)));
    }
}
