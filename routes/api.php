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
    Route::get('test_01', 'AdminController@test_01')->middleware('jwt.role:admin','jwt.auth');//所有账号信息
});

Route::prefix('school')->group(function () {
    Route::post('login', 'SchoolController@login');//管理员登录
    Route::post('registered', 'SchoolController@registered');//注册
    Route::get('test_01', 'AdminController@test_01')->middleware('jwt.role:school','jwt.auth');//所有账号信息
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
});

Route::get('export', 'AdminController@export');
Route::post('back_b_type', 'TypeController@type_back_b_type');
Route::post('back_c_name', 'TypeController@type_back_c_name');
