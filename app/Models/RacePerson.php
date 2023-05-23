<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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


}
