<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('admin')->group(function () {
    Route::post('login', 'AdminController@login');//管理员登录
    Route::post('registered', 'AdminController@registered');//注册
//    Route::get('test_01', 'AdminController@test_01')->middleware('jwt.role:admin','jwt.auth');//所有账号信息
});

Route::prefix('school')->group(function () {
    Route::post('login', 'SchoolController@login');//管理员登录
    Route::post('registered', 'SchoolController@registered');//注册
//    Route::get('test_01', 'AdminController@test_01')->middleware('jwt.role:school','jwt.auth');//所有账号信息
});
//Route::get('admin/test_01', 'AdminController@test_01')->middleware('jwt.role:admin','jwt.auth');//所有账号信息
Route::middleware('jwt.role:admin','jwt.auth')->prefix('admin')->group(function () {
    Route::post('add_school', 'SchoolController@add_school');//添加学校账号
    Route::get('find_all_school', 'AdminController@find_all_school');//添加学校账号
    Route::post('initial_page', 'AdminController@initial_page');//初始查看
    Route::post('reset_school_password', 'AdminController@reset_school_password');//重置密码
    Route::post('delete_school_account', 'AdminController@delete_school_account');//删除学校账号
    Route::post('add_competition_items', 'AdminController@add_competition_items');//添加项目
    Route::post('update_competition_items', 'AdminController@update_competition_items');//修改项目
    Route::post('delete_competition_items', 'AdminController@delete_competition_items');//删除项目
    Route::post('type_fuzzy_queries', 'AdminController@type_fuzzy_queries');//模糊查询
    Route::post('find_school_fuzzy_queries', 'AdminController@find_school_fuzzy_queries');//模糊查询比赛项目
    Route::post('array_delete_competition_items', 'AdminController@array_delete_competition_items');//批量删除比赛项目
    Route::post('array_school_delete', 'AdminController@array_school_delete');//批量删除学校账号
    Route::post('update_school_account', 'AdminController@update_school_account');//修改学校账号
    Route::post('item_query_button', 'AdminController@item_query_button');//查询比赛项目按钮
});
Route::middleware('jwt.role:user','jwt.auth')->prefix('school')->group(function (){
//    Route::post('login','SchoolController@login');//学校登录
    Route::post('judgment_first','SchoolController@judgment_first');//判断是否第一次进
    Route::post('update','SchoolController@update');//第一次进入修改密码
    Route::post('update_information','SchoolController@update_information');//修改比赛信息
    Route::post('sign_up','SchoolController@sign_up');//比赛报名
    Route::post('delete','SchoolController@delete');//删除比赛信息
    Route::post('array_application','SchoolController@array_application');//批量报名
    Route::post('array_delete','SchoolController@array_delete');//批量删除比赛信息
    Route::post('application_state','SchoolController@application_state');//比赛状态按钮
    Route::post('fuzzy_queries','SchoolController@fuzzy_queries');//模糊查询
    Route::post('export_excel','SchoolController@export_excel');//导出excel
    Route::post('receive_mailbox','SchoolController@receive_mailbox');//获取验证码按钮
    Route::post('details_page','SchoolController@details_page');//详情页面
    Route::post('forget_password','SchoolController@forget_password');//忘记密码
    Route::post('forget_password_email','SchoolController@forget_password_email');//忘记密码的获取验证码
//    Route::get('test_01', 'AdminController@test_01')->middleware('jwt.role:admin','jwt.auth');//所有账号信息
});
Route::get('export', 'AdminController@export');
Route::post('back_b_type', 'TypeController@type_back_b_type');
Route::post('back_c_name', 'TypeController@type_back_c_name');
Route::post('receive_mailbox','SchoolController@receive_mailbox');//获取验证码按钮
Route::post('detail','SchoolController@detail');//详情
Route::post('/school/forget_password_notoken','SchoolController@forget_password_notoken');//忘记密码
Route::post('/school/forget_password_email_notoken','SchoolController@forget_password_email_notoken');//忘记密码的邮箱认证

