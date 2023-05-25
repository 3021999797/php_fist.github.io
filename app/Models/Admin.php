<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{

    public $timestamps = true;
    protected $table = 'admin';
    protected $guarded = [];
    protected $hidden = [
        'password',
    ];
    public static function checknumber($request)
    {
        $admin_account = $request['account'];
        try{
            $count = Admin::select('account')
                ->where('account',$admin_account)
                ->count();
            return $count;
        }catch (\Exception $e) {
            logError("账号查询失败！", [$e->getMessage()]);
            return false;
        }
    }

    public static function createUser($array = [])
    {
        try {
            $student_id = self::create($array)->id;
            return $student_id ?
                $student_id :
                false;
        } catch (\Exception $e) {
            logError('添加用户失败!', [$e->getMessage()]);
            die($e->getMessage());
            return false;
        }
    }

//    public static function find_initial_page()
//    {
//        try {
//            $registration_information = DB::table('race_person')
//                ->join('race_type_name','race_person.race_id','=','race_type_name.c_id')
//                ->select('race_person.*', 'race_type_name.*')
//                ->get();
//            return $registration_information ?
//                $registration_information :
//                false;
//        } catch (\Exception $e) {
//            die($e->getMessage());
//            return false;
//        }
//    }

    public static function reset_school_pd($id)
    {
        $user = School::find($id);
        $user->password = bcrypt('123');
        $user->save();
    }

    public static function delete_account($request)
    {
        try{
            $count = DB::table('race_person')
                ->select('school_name')
                ->where('school_name',$request['school_name'])
                ->count();
            if(!$count){
                $user = School::find($request['id']);
                $user->delete();
                return true;
            }
            return false;
        }catch (\Exception $e) {
            logError("账号查询失败！", [$e->getMessage()]);
            return false;
        }

    }
//    //查看是否已经有这个比赛了
//    public static function check_project($request)
//    {
//        try{
//            $count = DB::table('race_type_name')
//                ->select('a_type','b_type','c_name')
//                ->where('a_type',$request['a_type'])
//                ->where('b_type',$request['b_type'])
//                ->where('c_name',$request['c_name'])
//                ->count();
//            return $count;
//        }catch (\Exception $e) {
//            logError("账号查询失败！", [$e->getMessage()]);
//            return false;
//        }
//    }
//    //添加比赛
//    public static function add_project($request)
//    {
//        try {
//            $project_id = RaceTypeName::create([
//                    'a_type'=>$request['a_type'],
//                    'b_type'=>$request['b_type'],
//                    'c_name'=>$request['c_name'],
//                ])->c_id;
//            return $project_id ?
//                $project_id :
//                false;
//        } catch (\Exception $e) {
//            logError('添加用户失败!', [$e->getMessage()]);
//            die($e->getMessage());
//            return false;
//        }
//    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
         return ['role' => 'admin'];
    }



}
