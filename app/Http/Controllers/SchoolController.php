<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    //
    /**
     * 注册
     * @param Request $registeredRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function add_school(Request $registeredRequest)
    {
        $count = School::checknumber($registeredRequest);   //检测账号密码是否存在
        if ($count == 0) {
            $school = School::createUser($registeredRequest);
            return $school ?
                json_success('注册成功!', $school, 200) :
                json_fail('注册失败!', null, 100);
        } else {
            return
                json_success('注册失败!该学校已经注册过了！', null, 100);
        }
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = self::credentials($request);   //从前端获取账号密码
        $token = auth('school')->attempt($credentials);   //获取token
        if ($token){
            $array_school = [
                'account'=>$request['account'],
                'token'=>$token,
            ];
            $res = School::backinformation($array_school);

            return json_success('登录成功!', $res, 200);
        }
        return json_fail('登录失败!账号或密码错误', null, 100);
    }
//    //封装token的返回方式
//    protected function respondWithToken($token, $msg)
//    {
//        return json_success($msg, array(
//            'token' => $token,
//            //设置权限  'token_type' => 'bearer',
//            'expires_in' => auth('api')->factory()->getTTL() * 60
//        ), 200);
//
//    }

    protected function credentials($request)   //从前端获取账号密码
    {
        return ['account' => $request['account'], 'password' => $request['password']];
    }
//    protected function userHandle($request)   //对密码进行哈希256加密
//    {
//        $registeredInfo = $request->except('password_confirmation');
//        $registeredInfo['password'] = bcrypt($registeredInfo['password']);
//        $registeredInfo['account'] = $registeredInfo['account'];
//
//        return $registeredInfo;
//    }
}
