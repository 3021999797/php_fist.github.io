<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Contracts\JWTSubject;

class School extends Authenticatable implements JWTSubject
{
    use SoftDeletes;
    /**   * 需要被转换成日期的属性
     *  @var array   */
    protected $dates = ['deleted_at'];
    protected $table = "school";
    // 指定开启时间戳
    public $timestamps = true;
    // 指定主键
    protected $primaryKey = "id";
    // 指定不允许自动填充的字段，字段修改的黑名单
    protected $guarded = [];
//    protected $fillable = [
//        'id','school_name', 'school_email','account','password'
//    ];




    public function getJWTCustomClaims()
    {
        return ['role'=>'user'];
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public static function createUser($array = [])
    {
        try {
            $school_id = School::create([
                'school_name' => $array['school_name'],
                'account'=>$array['account'],
                'school_email' => 'fist@fish.com',
                'password' => bcrypt('123'),
            ]);
            $array_school = [
                'school_id'  =>$school_id->id,
                'school_name'=>$school_id->school_name,
                'account'=>$school_id->account,
            ];
//            $student_id = self::create($array)->id;
            return $array_school ?
                $array_school :
                false;
        } catch (\Exception $e) {
            logError('添加用户失败!', [$e->getMessage()]);
            die($e->getMessage());
            return false;
        }
    }

    /**
     * 查询该工号是否已经注册
     * 返回该工号注册过的个数
     * @param $request
     * @return false
     */
    public static function checknumber($request)
    {
        $student_job_number = $request['school_name'];
        try{
            $count = School::select('school_name','account')
                ->where('school_name',$student_job_number)
                ->orwhere('account',$request['account'])
                ->count();
            //echo "该账号存在个数：".$count;
            //echo "\n";
            return $count;
        }catch (\Exception $e) {
            logError("账号查询失败！", [$e->getMessage()]);
            return false;
        }
    }
    public static function backinformation($request)
    {
            $count = DB::table('school')
                ->where('account',$request['account'])
                ->get();
        try {
            $res = [
                'id' => $count[0]->id,
                'school_name' => $count[0]->school_name,
                'account' => $count[0]->account,
                'token' => $request['token'],
            ];
        } catch (\Exception $e) {
            dd($e);
        }
            return $res;

    }
    public static function account_search($request)
    {
        $project = self::where('school_name', 'like', '%'.$request['search'].'%')
            ->orWhere('account', 'like', '%'.$request['search'].'%')
            ->get();
        return $project;
    }
    //查看数组里的比赛能不能删
    public static function array_can_delete($array = [])
    {
        try{
            $user = RacePerson::whereIn('school_name',$array)->get();
            return $user;
        }catch (\Exception $e) {
            logError("账号查询失败！", [$e->getMessage()]);
            return false;
        }
    }
    //批量删除学校账号
    public static function array_delete($array = [])
    {
        try{
            $count = School::whereIn('school_name',$array)->update(['deleted_at' => now()]);
            return $count;
        }catch (\Exception $e) {
            logError("删除失败！", [$e->getMessage()]);
            return false;
        }
    }
    //修改学校账号
    public static function update_account($request)
    {
        try{
            $school = School::find($request['id']);
            $school->school_name = $request['school_name'];
            $school->account = $request['account'];
            $school->password = bcrypt($request['password']);
            return $school->save();
        }catch (\Exception $e) {
            logError("删除失败！", [$e->getMessage()]);
            return false;
        }
    }
    public static function judgment($request)
    {
        $email="fist@fish.com";
//    $result=self::select("school_email")->where('id',$request['id'])->first();
//    dd($result);
//        $result=DB::table('school')->where('school_email',$email)->first();
        $result=School::where('account',$request['account'])->get();
        $judgment_email=$result[0]->school_email;
        if($judgment_email==$email)
            return 0;
        else
            return 1;
    }
    public static function judegment_email($request)
    {
        $random=rand(100000,999999);
        $email=$request->input("email");
        Mail::raw("您的验证码是:".$random, function($message) use ($email) {
            $message->to($email)->subject('验证码');
        });
//            $email = $request['school_email'];//前端获取邮箱
//            $code = rand(1000, 9999);//产生随机数
//            Mail::raw('验证码为:' . $code, function ($message) {
//                $message->subject('验证码提醒');
//                $message->to('$email');
//            });//发送邮箱
        return bcrypt($random);
    }
    public static function update_email_password($request)
    {
        try{
            $project=self::find($request['id']);//找到学校对应id
            $project->school_email=$request['school_email'];
            $project->password=bcrypt($request['password']);
            $project->save();
            return $project->id ?
                $project->id:
                false;
        }catch (\Exception $e) {
            logError('修改密码失败!', [$e->getMessage()]);
            die($e->getMessage());
            return false;
        }
    }
    public static function update_password($request)
    {
        try{
//            $username=$request->input("account");
//            $account=DB::select("select id from school where account = '$username'");
//            $count=self::find($account);
//            $count->password=bcrypt($request['password']);
//            $count->save();
//            $count = self::select("id")
//                ->where('account',$request['account'])
//                ->get();
////            $count=School::select($request['account']);
////            return $count;

            $project=self::where('school_email',$request['school_email'])
                ->update(['password'=>bcrypt($request['password'])]);
//            $project->password=bcrypt($request['password']);
//            $project->save();
            return $project?
                $project:
                false;

        } catch (\Exception $e) {
            logError('修改密码失败!', [$e->getMessage()]);
            die($e->getMessage());
            return false;
        }
    }

//判断邮箱是否和数据库中相等
    public static function check_email($request)
    {
        try {
            $result=School::where('account',$request['account'])->get();
                $email=$request['email'];
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

    public static function check_user($request)
    {
        try {
            $project=self::where('account',$request['account'])->count();
            return $project ?
                $project:
                false;
        }catch (\Exception $e) {
            logError('修改密码失败!', [$e->getMessage()]);
            die($e->getMessage());
            return false;
        }
    }


}
