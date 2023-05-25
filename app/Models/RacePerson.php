<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RacePerson extends Model
{
    use SoftDeletes;
    /**   * 需要被转换成日期的属性
     *  @var array   */
    protected $dates = ['deleted_at'];
    protected $table = "race_person";
    // 指定开启时间戳
    public $timestamps = true;
    // 指定主键
    protected $primaryKey = "u_id";
    // 指定不允许自动填充的字段，字段修改的黑名单
    protected $guarded = [];

    //查看是否已经有人报这个比赛了
    public static function check_project($request)
    {
        try{
            $project = self::where('race_id',$request['c_id'])->count();
            return $project;
        }catch (\Exception $e) {
            logError("账号查询失败！", [$e->getMessage()]);
            return false;
        }
    }

    //比赛的信息模糊查询
    public static function people_search($request)
    {
        $project = self::Where('rise_state','1')
            ->Where('job_profession', 'like', '%'.$request['search_job_profession'].'%')
            ->Where('degree', 'like', '%'.$request['search_degree'].'%')
//            ->orWhere('instructor', 'like', '%'.$request['search'].'%')
            ->Where('race_id', 'like', '%'.$request['search_race_id'].'%')
            ->join('race_type_name','race_id','=','race_type_name.c_id')
//            ->select('race_person.*', 'race_type_name.*')
            ->get();
        return $project;
    }
    public static function add_person( $request)
    {
        try{

//            $check=RacePerson::check_project($request);
//            if($check)
//            {
//                return 0;
//            }
            $result=School::where('id',$request['school_id'])->get();
//            return $result;
            $project=self::create([
//                'u_id'=>$request["u_id"],
                'school_name'=>$result[0]->school_name,
                'name'=>$request['username'],
                'gender'=>$request['gender'],
                'age'=>$request['age'],
                'ethnic_groups'=>$request['ethnic_groups'],
                'job_profession'=>$request['job_profession'],
                'telephone_number'=>$request['telephone_number'],
                'identity_card'=>$request['identity_card'],
                'participating_group'=>$request['participating_group'],
                'degree'=>$request['degree'],
                'instructor'=>$request['instructor'],
                'race_id'=>$request['race_id'],
            ])->save();
            self::where('race_id',$request['race_id'])->update(['rise_state'=>'1']);
            $result=RaceTypeName::create([
                'c_id'=>$request['race_id'],
                'a_type'=>$request['a_type'],
                'b_type'=>$request['b_type'],
                'c_name'=>$request['c_name'],
            ])->save();

            if($project&&$result)
                return 1;
            else
                return 0;

//           $project->school_name=$request["school_name"];
//            $project->name=$request['name'];
//            $project->gender=$request['gender'];
//            $project->age=$request['age'];
//            $project->ethnic_groups=$request['ethnic_groups'];
//            $project->job_profession=$request['job_profession'];
//            $project->telephone_number=$request['telephone_number'];
//            $project->identity_card=$request['identity_card'];
//            $project->participating_group=$request['participating_group'];
//            $project->degree=$request['degree'];
//            $project->degree=$request['instructor'];
//            $project->race_id=$request['race_id'];
//            $project->save();
        }catch (\Exception $e) {
            logError('添加用户失败!', [$e->getMessage()]);
            die($e->getMessage());
            return false;
        }
    }
    public static function select_excel($request)
    {
        try {
            $school = self::select('name as 姓名', 'participating_group as 学生组/教师组', 'degree as 学历',
                'telephone_number as 联系电话','a_type as 比赛类型','c_name as 比赛项目')
                ->join('race_type_name','race_person.race_id','=','race_type_name.c_id')
                ->where('rise_state', '1')
                ->get();
//            $c_id=self::where('school_name',$request['school_name'])->get();
//            $school2 = RaceTypeName::where('c_id', $c_id[0]->race_id)->get('a_type', 'c_name');
//            $school_sum = $school1->concat($school2);
            return $school;
        } catch (\Exception $e) {
            logError('查询失败!', [$e->getMessage()]);
            return false;
        }
    }
    public static function dim_school($data)
    {
        try {
            $result=RacePerson::select("name as 姓名","participating_group as 参赛组","degree as 学历",
                "telephone_number as 联系电话","a_type as 比赛类型","c_name as 比赛项目" )
//                ->where('school_name','like','%'.$name.'%')
                ->where('name','like','%'.$data.'%')
                ->orwhere('participating_group','like','%'.$data.'%')
                ->orwhere('degree','like','%'.$data.'%')
                ->orwhere('telephone_number','like','%'.$data.'%')
                ->join('race_type_name','race_person.race_id','=','race_type_name.c_id')
                ->orwhere('a_type','like','%'.$data.'%')
                ->orwhere('c_name','like','%'.$data.'%')
                ->get();
            return $result;
        }catch (\Exception $e) {
            logError('删除失败失败!', [$e->getMessage()]);
        }

    }
    public static function change_state($request)
    {
        try {
            $result=self::where(['u_id'=>$request['u_id']])->first();
            $result->rise_state=$request['rise_state'];
            $result->save();
            return $result->save() ?
                $result->save():
                false;
        }catch (\Exception $e) {
            logError('修改失败!', [$e->getMessage()]);
        }

    }
    public static function school_information_delete($u_id)
    {
        try{
            self::where('u_id',$u_id)->delete();
        }catch (\Exception $e) {
            logError('删除失败失败!', [$e->getMessage()]);
        }
    }
    public static function school_inf_state($u_id)
    {
        try{
            self::where('u_id',$u_id)->update(['rise_state'=>'1']);
        }catch (\Exception $e) {
            logError('添加用户报名信息失败!', [$e->getMessage()]);
        }
    }
    public static function delete_information($request)
    {
        $user=RacePerson::find($request['u_id']);
        if($user)
        {
            $user->delete();
            return true;
        }
        else
            return false;
    }
    public static function look_for_all($id)
    {
    try {
        $result=self::select('name as 姓名','gender as 性别','age as 年龄','ethnic_groups as 民族',
            'job_profession as 职业','telephone_number as 电话号码','identity_card as 身份证',
            'participating_group as 参赛组','degree as 学历','instructor as 指导老师',
            'a_type as 比赛大类','b_type as 比赛类型','c_name as 比赛项目')
            ->join('race_type_name','race_person.race_id','=','race_type_name.c_id')
            ->where('race_id',$id)
            ->get();
        return $result;
    }catch (\Exception $e) {
        logError('查询失败!', [$e->getMessage()]);
        return false;
    }
}

//判断用户是否存在
    public static function check_uesr($request)
    {
        try {
            $project=$request['u_id'];
            return $project;
        }catch (\Exception $e) {
            logError('不存在!', [$e->getMessage()]);
            return false;
        }
    }

    //判断邮箱是否和数据库中相等
    public static function check_email($request)
    {
        try {
            $result=School::where('account',$request['account'])->get();
            $email=$request['school_email'];
            if($result[0]->school_email===$email)
            {
                $random=rand(100000,999999);
                Mail::raw("您的验证码是:".$random, function($message) use ($email) {
                    $message->to($email)->subject('验证码');
                });
                return bcrypt($random);
            }
            else{
                return false;
            }
        }catch (\Exception $e) {
            logError('修改密码失败!', [$e->getMessage()]);
            die($e->getMessage());
            return false;
        }
    }

    public static function look_for_details($school_name)
    {
        try {
            $school = self::select('name as 姓名', 'participating_group as 学生组/教师组', 'degree as 学历',
                'telephone_number as 联系电话','rise_state as 状态','a_type as 比赛类型','c_name as 比赛项目')
                ->join('race_type_name','race_person.race_id','=','race_type_name.c_id')
                ->where('school_name',$school_name)
                ->get();
            return $school;
        }catch (\Exception $e) {
            logError('不存在!', [$e->getMessage()]);
            return false;
        }
    }

}
