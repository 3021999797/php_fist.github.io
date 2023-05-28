<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCompetitionItems;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\ItemQueryButton;
use App\Http\Requests\UpdateCompetitionItems;
use App\Http\Requests\UpdateSchoolAccount;
use App\Models\Admin;
use App\Models\RacePerson;
use App\Models\RaceTypeName;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
//use Rap2hpoutre\FastExcel\SheetCollection;
class AdminController extends Controller
{
    //
    /**
     * 注册
     * @param Request $registeredRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function registered(Request $registeredRequest)
    {
        $count = Admin::checknumber($registeredRequest);   //检测账号密码是否存在
        if ($count == 0) {
            $admin = Admin::createUser(self::userHandle($registeredRequest));
            return $admin ?
                json_success('注册成功!', $admin, 200) :
                json_fail('注册失败!', null, 100);
        } else {
            return
                json_success('注册失败!该工号已经注册过了！', null, 100);
        }
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AdminLoginRequest $request)
    {
        $credentials = self::credentials($request);   //从前端获取账号密码
        $token = auth('admin')->attempt($credentials);   //获取token
        return $token ?
            json_success('登录成功!', $token, 200) :
            json_fail('登录失败!账号或密码错误', null, 100);
    }


    //封装token的返回方式
    protected function respondWithToken($token, $msg)
    {
        return json_success($msg, array(
            'token' => $token,
            //设置权限  'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ), 200);

    }

    protected function credentials($request)   //从前端获取账号密码
    {
        return ['account' => $request['account'], 'password' => $request['password']];
    }
    protected function userHandle($request)   //对密码进行哈希256加密
    {
        $registeredInfo = $request->except('password_confirmation');
        $registeredInfo['password'] = bcrypt($registeredInfo['password']);
        $registeredInfo['account'] = $registeredInfo['account'];
        return $registeredInfo;
    }

    //渲染所有学校信息
    public function find_all_school()
    {
        $allData = School::all();
        return $allData ?
            json_success('登录成功!', $allData, 200) :
            json_fail('登录失败!账号或密码错误', null, 100);
    }
    //初始显示所有比赛信息加搜索按钮
    public function initial_page(Request $request)
    {
        $allData = RacePerson::people_search($request);
        return $allData ?
            json_success('查询成功!', $allData, 200) :
            json_fail('查询失败', $allData, 100);
    }

    //重置学校密码
    public function reset_school_password(Request $request)
    {
        Admin::reset_school_pd($request['id']);
        return json_success('重置成功!', null, 200);

    }
    //删除学校账号
    public function delete_school_account(Request $request)
    {
        return Admin::delete_account($request) ?
            json_success('删除成功!', null, 200) :
            json_fail('删除失败，学校有报名信息', null, 100);

    }
    //添加比赛项目
    public function add_competition_items(AddCompetitionItems $request)
    {
        $count = RaceTypeName::check_project($request);   //检测账号密码是否存在
        if ($count == 0) {
            $project = RaceTypeName::add_project($request);
            return $project ?
                json_success('注册成功!', $project, 200) :
                json_fail('注册失败!', $project, 100);
        } else {
            return
                json_success('注册失败!该项目已经注册过了！', null, 100);
        }
    }
    //修改比赛项目
    public function update_competition_items(UpdateCompetitionItems $request)
    {
        $count = RaceTypeName::check_project($request);   //检测账号密码是否存在
        if ($count == 0) {
            $project = RaceTypeName::update_project($request);
            return $project ?
                json_success('修改成功!', $project, 200) :
                json_fail('修改失败!', $project, 100);
        } else {
            return
                json_success('修改失败!该项目已经存在！', null, 100);
        }
    }
    //删除比赛项目
    public function delete_competition_items(Request $request)
    {
        $count = RacePerson::check_project($request);   //检测账号密码是否存在
        if ($count == 0) {

            return  RaceTypeName::delete_account($request)?
                json_success('删除成功!', null, 200) :
                json_fail('删除失败!', null, 100);
        } else {
            return
                json_success('删除失败!该项目已经存在！', null, 100);
        }
    }
    //删除比赛信息
    public function delete_person_information(Request $request)
    {
            $count = RacePerson::delete_person($request);
            return  $count?
                json_success('删除成功!', $count, 200) :
                json_fail('删除失败!', $count, 100);
    }
    //模糊查询比赛项目
    public function type_fuzzy_queries(Request $request)
    {    $project = RaceTypeName::project_search($request);
        return  json_success('查询成功',$project,200);
    }
    //模糊查询学校
    public function find_school_fuzzy_queries(Request $request)
    {
        $project = School::account_search($request);
        return  json_success('查询成功',$project,200);
    }

    //批量删除比赛类型
    public function array_delete_competition_items(Request $request)
    {
        $ids = $request->input('ids', []);
        if(empty($ids)){
            return
                json_fail('删除失败!，没有选择要删除的数据', null, 100) ;
        }
        $race=RaceTypeName::array_can_delete($ids);
        if($race->count() !=0){
            return
                json_fail('删除失败!比赛已经有人报名！', $race, 100);
        }
        $count = RaceTypeName::array_delete($ids);
        if($count== count($ids)&&$count!=0){
            return
                json_success('删除成功!', $count, 200) ;
        }
        return json_fail('删除失败!', null, 100);
    }
    //批量删除学校账号
    public function array_school_delete(Request $request)
    {
        $names = $request->input('names', []);
        if(empty($names)){
            return
                json_fail('删除失败!，没有选择要删除的数据', null, 100) ;
        }
        $race=School::array_can_delete($names);
        if($race->count() !=0){
            return
                json_fail('删除失败!比赛已经有人报名！', $race, 100);
        }
        $count = School::array_delete($names);
        if($count== count($names)&&$count!=0){
            return
                json_success('删除成功!', $count, 200) ;
        }
        return json_fail('删除失败!', null, 100);
    }
    //批量删除比赛信息
    public function delete_match_information(Request $request)
    {
        $ids = $request->input('ids', []);
        if(empty($ids)){
            return
                json_fail('删除失败!，没有选择要删除的数据', null, 100) ;
        }
//        $race=RaceTypeName::array_can_delete($ids);
//        if($race->count() !=0){
//            return
//                json_fail('删除失败!比赛已经有人报名！', $race, 100);
//        }
        $count = RacePerson::delete_match($ids);
        if($count== count($ids)&&$count!=0){
            return
                json_success('删除成功!', $count, 200) ;
        }
        return json_fail('删除失败!', null, 100);
    }
    //修改学校账号
    public function update_school_account(UpdateSchoolAccount $request)
    {

        $count=School::checknumber($request);
        if($count!=0){
            return
                json_fail('修改失败!有同名学校或有账号相同！', $count, 100);
        }
        $count = School::update_account($request);
        return $count ?
            json_success('修改成功!', null, 200) :
            json_fail('修改失败!', null, 100);
    }

    public function export()
    {
        $data=RacePerson::Where('rise_state','1')
            ->join('race_type_name','race_id','=','race_type_name.c_id')
            ->get(['race_person.school_name as 学校','race_person.job_profession as 学生/教师组','race_person.gender as 性别','race_person.degree as 本科/专科','race_person.name as 姓名','race_type_name.a_type as 比赛类型','race_type_name.c_name as 比赛项目','race_person.telephone_number as 联系方式']);
//        $header = [
//            '学校',
//            '学生/教师组',
//            '本科/专科',
//            '姓名',
//            '比赛类型',
//            '比赛项目',
//            '联系方式'
//        ];
//        $sheets = new SheetCollection([
//            'Sheet1' => $data,
//        ]);
//        $sheets->prependRow($header);
////
//        return
//            json_success('删除成功!', $data, 200) ;
        return (new FastExcel($data))->download('所有报名人员' . '.xlsx');
//        return (new FastExcel($sheets))->withHeaders()->export('file.xlsx');
    }


    //查询比赛项目按钮
    public function item_query_button(ItemQueryButton $request)
    {    $project = RaceTypeName::query_button($request);
        return  json_success('查询成功',$project,200);
    }





}
