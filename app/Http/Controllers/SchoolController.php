<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSchool;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\ForgetPassword;
use App\Http\Requests\ForgetPasswordEmail;
use App\Http\Requests\RaceSignUp;
use App\Http\Requests\ReceiveMailBox;
use App\Http\Requests\UpdateRaceInformation;
use App\Http\Requests\UpdateSchoolByEmail;
use App\Models\Admin;
use App\Models\RacePerson;
use App\Models\RaceTypeName;
use App\Models\School;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class SchoolController extends Controller
{
    //
    /**
     * 注册
     * @param Request $registeredRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function add_school(AddSchool $registeredRequest)
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
    public function login(AdminLoginRequest $request)
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


    protected function credentials($request)   //从前端获取账号密码
    {
        return ['account' => $request['account'], 'password' => $request['password']];
    }

    //判断是否是第一次进入
    public function judgment_first(Request $request)
    {
        $check = School::judgment($request);//检查是否为第一次进入
        if($check==0)
        {
            return json_success('是第一次进',$check,200);
        }
        else{
            return json_fail('非第一次进入，可不修改',null,100);
        }
    }

//    //第一进入强制修改密码
//    public function update(Request $request)
//    {
//
//        $code = School::judegment_email($request);//返回验证码
//
//        $project = School::update_email_password($request);//更改密码和邮箱
//        return $code&&$project ?
//            json_success('修改密码成功!', $code, 200) :
//            json_fail('验证码错误!', null, 100);
//
//    }
    //第一进入强制修改密码
    public function update(UpdateSchoolByEmail $request)
    {
        $project = School::update_email_password($request);//更改密码和邮箱
        return $project ?
            json_success('修改密码成功!', $project, 200) :
            json_fail('验证码错误!', null, 100);
    }
//    //忘记密码
//    public function forget_password(Request $request)
//    {
//        $account=School::checknumber($request);
//        if($account)
//        {
//            $code = School::judegment_email($request);//返回验证码
//            $project=School::update_password($request);
//            return $code ?
//                json_success('修改密码成功!', $code, 200) :
//                json_fail('修改密码失败!', null, 100);
//        }
//        else{
//            json_fail('账号不存在！',null,100);
//        }
//    }
//忘记密码
    public function forget_password(ForgetPassword $request)
    {
        $account=School::checknumber($request);
        if($account)
        {
            $project=School::update_password($request);
            return $project ?
                json_success('修改密码成功!', $project, 200) :
                json_fail('修改密码失败!', null, 100);
        }
        else{
            json_fail('账号不存在！',null,100);
        }
    }
//比赛报名
    public function sign_up(RaceSignUp $request)
    {
        //检查是否有这个比赛id
        $check=RacePerson::check_project($request);
        if($check)
        {
            json_fail('用户已报名',null,100);
        }
        else{
            $project=RacePerson::add_person($request);
            if($project=='1')
            {
                return json_success('报名成功',$project,200);
            }else{
                return json_fail('报名失败',null,100);
            }
        }

    }
    //删除比赛信息
    public function delete(Request $request)
    {
        return RacePerson::delete_information($request) ?
            json_success('删除成功!', null, 200) :
            json_fail('删除失败', null, 100);
    }
//批量报名
    public function array_application(Request $request)
    {
        $array = explode(',',$request['array']);
        try {
            for($i=0;$i<count($array);$i++)    {
                RacePerson::school_inf_state($array[$i]);
            }
            return json_success('报名信息提交成功!', 'true', 200);
        }catch (\Exception $e) {
            logError('报名信息提交失败!', [$e->getMessage()]);
            return json_fail('报名信息提交失败!','false', 100 );
        }

    }
//批量删除
    public function array_delete(Request $request)
    {
        $array = explode(',',$request['array']);
        try {
            for($i=0;$i<count($array);$i++)    {
                RacePerson::school_information_delete($array[$i]);
            }
            return json_success('删除成功!', 'true', 200);
        }catch (\Exception $e) {
            logError('删除失败!', [$e->getMessage()]);
            return json_fail('删除失败!','false', 100 );
        }
    }
    //比赛状态按钮
    public function application_state(Request $request)
    {
        try {
            $project=RacePerson::change_state($request);
            return $project ?
                json_success('修改成功',$project,200):
                json_fail('修改失败',null,100);
        }catch (\Exception $e) {
            logError('失败!', [$e->getMessage()]);
            return json_fail('失败!','false', 100 );
        }
    }
    //修改比赛信息
    public function update_information(UpdateRaceInformation $request)
    {
        try {
            $check=RacePerson::check_uesr($request);
            if($check)
            {
                $id=$request['u_id'];
                $project=RacePerson::where(['u_id'=>$id])->first();
                $race_id=RacePerson::where(['u_id'=>$id])->get();
                $project->name=$request['name'];
                $project->gender=$request['gender'];
                $project->age=$request['age'];
                $project->ethnic_groups=$request['ethnic_groups'];
                $project->job_profession=$request['job_profession'];
                $project->telephone_number=$request['telephone_number'];
                $project->identity_card=$request['identity_card'];
                $project->participating_group=$request['participating_group'];
                $project->degree=$request['degree'];
                $project->instructor=$request['instructor'];
                $project->save();
                $result=RaceTypeName::where(['c_id'=>$race_id[0]->race_id])->first();
                $result->a_type=$request['a_type'];
                $result->b_type=$request['b_type'];
                $result->c_name=$request['c_name'];
                $result->save();
                return $project&&$result ?
                    json_success('修改成功', $project&&$result, 200) :
                    json_fail('修改失败', null, 100);
            }
            else{
                json_fail('用户不存在',null,100);
            }
        } catch (\Exception $e) {
            logError('失败!', [$e->getMessage()]);
            return json_fail('失败!', 'false', 100);
        }
    }
//模糊查询
    public function fuzzy_queries(Request $request)
    {
        try {
            $project = RacePerson::dim_school($request['data']);
           return $project ?
               json_success('查询成功',$project,200):
               json_fail('查询失败',null,100);

        } catch (\Exception $e) {
            logError('失败!', [$e->getMessage()]);
            return json_fail('失败!', 'false', 100);
        }
    }

    public function export_excel(Request $request)
    {

        $list = RacePerson::select_excel($request);

        return (new FastExcel($list))->download('比赛报名表' . '.xlsx');


    }
    //获取验证码按钮
    public function receive_mailbox(ReceiveMailBox $request)
    {
        try {
            $code = School::judegment_email($request);//返回验证码
            return $code ?
                json_success('成功',$code,200):
                json_fail('失败',null,100);
        }catch (\Exception $e) {
            return json_fail('失败!', null, 100);
        }
    }

    //详情
    public function detail(Request $request)
    {
        try {
            $result=RacePerson::check_uesr($request);
            if($result)
            {
                $project=RacePerson::look_for_all($request['u_id']);
                return $project ?
                    json_success('查询成功',$project,200):
                    json_fail('查询失败',null,100);
            }else{
                return json_fail('该用户不存在',null,100);
            }

        }catch (\Exception $e) {
            return json_fail('失败!', null, 100);
        }
    }


//忘记密码的获取验证码
    public function forget_password_email(ForgetPasswordEmail $request)
    {
        try {
            $project=School::check_email($request);
            return $project ?
                json_success('发送成功',$project,200):
                json_fail('邮箱错误',null,100);

        }catch (\Exception $e) {
            return json_fail('失败!', null, 100);
        }
    }


    //页面的详情
    public function details_page(Request $request)
    {
        try {
            $project=RacePerson::look_for_details($request['school_name']);
            return $project ?
                json_success('查询成功',$project,200):
                json_fail('查询失败',null,100);
        }catch (\Exception $e) {
            return json_fail('失败!', null, 100);
        }
    }

}
