<?php

namespace App\Http\Controllers;

use App\Models\RaceTypeName;
use Illuminate\Http\Request;

class TypeController extends Controller
{

    public function type_back_b_type(Request $request)
    {
        $b_type = RaceTypeName::back_b_type($request);   //检测账号密码是否存在
        if ($b_type->count() == 0) {
            return
                json_fail('查询失败!没有比赛类型数据', null, 100);
        } else {
            return
                json_success('查询成功！', $b_type, 200);
        }
    }
    public function type_back_c_name(Request $request)
    {
        $c_name = RaceTypeName::back_c_name($request);   //检测账号密码是否存在
        if ($c_name->count() == 0) {
            return
                json_fail('查询失败!没有比赛项目数据', null, 100);
        } else {
            return
                json_success('查询成功！', $c_name, 200);
        }
    }
}
