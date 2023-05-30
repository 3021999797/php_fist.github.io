<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class RaceTypeName extends Model
{
    use SoftDeletes;
    /**   * 需要被转换成日期的属性
     *  @var array   */
    protected $dates = ['deleted_at'];
    protected $table = "race_type_name";
    // 指定开启时间戳
    public $timestamps = true;
    // 指定主键
    protected $primaryKey = "c_id";
    // 指定不允许自动填充的字段，字段修改的黑名单
    protected $guarded = [];

    //查看是否已经有这个比赛了
    public static function check_project($request)
    {
        try{
            $count = DB::table('race_type_name')
                ->select('a_type','b_type','c_name')
                ->where('a_type',$request['a_type'])
                ->where('b_type',$request['b_type'])
                ->where('c_name',$request['c_name'])
                ->count();
            return $count;
        }catch (\Exception $e) {
            logError("账号查询失败！", [$e->getMessage()]);
            return false;
        }
    }

    //添加比赛
    public static function add_project($request)
    {
        try {
            $project_id = self::create([
                'a_type'=>$request['a_type'],
                'b_type'=>$request['b_type'],
                'c_name'=>$request['c_name'],
            ])->c_id;
            return $project_id ?
                $project_id :
                false;
        } catch (\Exception $e) {
            logError('添加用户失败!', [$e->getMessage()]);
            die($e->getMessage());
            return false;
        }
    }
    //修改比赛
    public static function update_project($request)
    {
        try {
            $project = self::find($request['c_id']);
            $project->a_type = $request['a_type'];
            $project->b_type = $request['b_type'];
            $project->c_name = $request['c_name'];
            $project->save();
            return $project->c_id ?
                $project->c_id :
                false;
        } catch (\Exception $e) {
            logError('添加用户失败!', [$e->getMessage()]);
            die($e->getMessage());
            return false;
        }
    }
    public static function delete_account($request)
    {
        try{
            $user = RaceTypeName::find($request['c_id']);
            $user->delete();
            return true;
        }catch (\Exception $e) {
            logError("账号查询失败！", [$e->getMessage()]);
            return false;
        }
    }

    //查看数组里的比赛能不能删
    public static function array_can_delete($array = [])
    {
        try{
            $user = RacePerson::whereIn('race_id',$array)->get();
            return $user;
        }catch (\Exception $e) {
            logError("账号查询失败！", [$e->getMessage()]);
            return false;
        }
    }


    //批量删除比赛类型
    public static function array_delete($array = [])
    {
        try{
            $count = RaceTypeName::whereIn('c_id',$array)->update(['deleted_at' => now()]);
            return $count;
        }catch (\Exception $e) {
            logError("删除失败！", [$e->getMessage()]);
            return false;
        }
    }

    //返回b类比赛类型
    public static function back_b_type($request)
    {
        try{
            $count = RaceTypeName::where('a_type',$request['a_type'])->groupBy('b_type')->get('b_type');
            return $count;
        }catch (\Exception $e) {
            logError("删除失败！", [$e->getMessage()]);
            return false;
        }
    }
    //返回c类比赛项目
    public static function back_c_name($request)
    {
        try{
            $count = RaceTypeName::select('c_id', 'c_name')->where('b_type',$request['b_type'])->get();
            return $count;
        }catch (\Exception $e) {
            logError("删除失败！", [$e->getMessage()]);
            return false;
        }
    }
    //查询比赛项目按钮
    public static function query_button($request)
    {
        try{
            $count = RaceTypeName::where('a_type',$request['a_type'])
                ->where('b_type',$request['b_type'])
                ->get();
            return $count;
        }catch (\Exception $e) {
            logError("删除失败！", [$e->getMessage()]);
            return false;
        }
    }

    //比赛类型模糊查询
//    public static function project_search($request)
//    {
//        $project = self::where(function ($query) use ($request) {
//            $query->where('a_type', 'like', '%' . $request['search'] . '%')
//                ->orWhere('b_type', 'like', '%' . $request['search'] . '%')
//                ->orWhere('c_name', 'like', '%' . $request['search'] . '%');
//        })->orWhere('a_type',$request['a_type'] )
//            ->orWhere('b_type',$request['b_type'] )
//            ->get();
//
//
//
////        $project = self::orWhere('a_type', 'like' ,'%'.$request['a_type'].'%' )
////            ->orWhere('b_type', 'like' ,'%'.$request['b_type'].'%' )
////            ->orwhereRaw("a_type like ? or b_type like ? or c_name like ?",['%'.$request['search'].'%','%'.$request['search'].'%','%'.$request['search'].'%'])
//////            ->Where('degree', 'like', '%'.$request['search_degree'].'%')
////////            ->orWhere('instructor', 'like', '%'.$request['search'].'%')
//////            ->Where('race_id', 'like', '%'.$request['search_race_id'].'%')
//////            ->join('race_type_name','race_id','=','race_type_name.c_id')
////////            ->select('race_person.*', 'race_type_name.*')
////            ->get();
//        return $project;
//    }
    public static function project_search($request)
{
    $project = self::where('a_type', 'like','%'.$request['search'].'%' )
        ->orWhere('b_type', 'like','%'.$request['search'].'%' )
        ->orWhere('c_name', 'like', '%'.$request['search'].'%')
        ->get();
    return $project;
}
}
