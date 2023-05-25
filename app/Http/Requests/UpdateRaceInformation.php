<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRaceInformation extends FormRequest
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
            'u_id'=>'required|integer',
            'username'=>'required|string|min:0|max:50',
            'gender'=>'required',
            'age'=>'required|integer|min:1|max:120',
            'ethnic_groups'=>'required|string|min:0|max:50',
            'job_profession'=>'required|string|min:0|max:50',
            'telephone_number'=>['required', 'regex:/^[0-9]{11}$/'],
            'identity_card'=>['required', 'regex:/^[0-9]{18}$/'],
            'participating_group'=>'required|string',
            'degree'=>'required|string|max:50',
            'instructor'=>'required|string|min:0|max:50',
            'a_type'=>'required|string',
            'b_type'=>'required|string',
            'c_name'=>'required|string',
        ];
    }
    public function messages()
    {
        return [
            'u_id.required'=>'u_id不能为空',
            'u_id.integer'=>'u_id非整形',
            'username.required'=>'姓名不能为空',
            'username.string'=>'名字只能为字符串',
            'username.min'=>'名字最小不能小于0个字符',
            'username.max'=>'名字不能超过50个字符',
            'gender.required'=>'性别不能为空',
            'age.required'=>'性别不能为空',
            'age.integer'=>'比赛id非整形',
            'age.min'=>'年龄不能小于1',
            'age,max'=>'年龄不能大于120',
            'ethnic_groups.required'=>'民族不能为空',
            'ethnic_groups.string'=>'学历只能为字符串',
            'ethnic_groups.min'=>'学历不能小于0个字符',
            'ethnic_groups.max'=>'民族不能超过50个字符',
            'job_profession.required'=>'职业不能为空',
            'job_profession.string'=>'职业只能为字符串',
            'job_profession.min'=>'职业不能小于0个字符',
            'job_profession.max'=>'职业不能超过50个字符',
            'telephone_number.required'=>'电话号码不能为空',
            'telephone_number.regex'=>'电话号码格式不对',
            'identity_card.required'=>'身份证不能为空',
            'identity_card.regex'=>'身份证格式不对',
            'participating_group.required'=>'参赛组不能为空',
            'degree.required'=>'学历不能为空',
            'degree.max'=>'学历不能超过50个字符',
            'instructor.required'=>'指导老师不能为空',
            'instructor.string'=>'名字只能为字符串',
            'instructor.min'=>'指导老师不能小于0个字符',
            'instructor.max'=>'指导老师不能超过50个字符',
            'a_type.required'=>'比赛大类不能为空',
            'a_type.string'=>'比赛大类只能为字符串',
            'b_type.required'=>'比赛类型不能为空',
            'b_type.string'=>'比赛类型只能为字符串',
            'c_name.required'=>'比赛项目不能为空',
            'c_name.string'=>'比赛项目只能为字符串',
        ];
    }
    protected function failedValidation(Validator $validator)
    {

        throw(new HttpResponseException(json_fail('参数错误', $validator->errors()->all(), 422)));
    }
}
