<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * base route > welcome
 */
Route::get('/', function () {
    return view('welcome');
});

/**
 * home page route
 */
Route::get('/home', 'HomeController@index')->name('home');

/**
 * activate routes from authentication
 */
Auth::routes();

/**
 * primary list page for inationsusers
 */
Route::get('/inationsusers', array('as'=>'inationuser.list', 'uses'=> 'InationsusersController@index'));

/**
 * create new inationsusers
 */
Route::post('/inationsusers', array('as'=>'ination.newuser', 'uses'=> 'InationsusersController@create'));

/**
 * get the inationsusers info for the edit modal window
 */
Route::get('/inationsusers/{user_id}', array('as'=>'ination.edituser', 'uses'=> 'InationsusersController@edit'));

/**
 * save a new inationsuser in the system
 */
Route::put('/inationsusers/{user_id}', array('as'=>'ination.storeuser', 'uses'=> 'InationsusersController@store'));

/**
 * removes a inationsuser from the system
 */
Route::delete('/inationsusers/{user_id}', array('as'=>'ination.destroyuser', 'uses'=> 'InationsusersController@destroy'));

//Ajax requests routes for inationsusers > for modal windows
/**
 * get the groups that are not connected to the inationsuser > add new group modal window
 */
Route::get('/inationsusers/addgroup/{user_id}', array('as'=>'ination.groupuser', 'uses'=> 'InationsusersController@addGroup'));

/**
 * save a new connection between inationsuser and group
 */
Route::put('/inationsusers/addgroup/{user_id}/{group_id}', array('as'=>'ination.addgroupuser', 'uses'=> 'InationsusersController@storeGroupUser'));

/**
 * get the groups that are connected to the inationsuser > remove group modal window
 */
Route::get('/inationsusers/remgroup/{user_id}', array('as'=>'ination.remgroupuser', 'uses'=> 'InationsusersController@remGroup'));

/**
 * removes a connection between inationsuser and group
 */
Route::put('/inationsusers/remgroup/{user_id}/{group_id}', array('as'=>'ination.removegroupuser', 'uses'=> 'InationsusersController@destroyGroupUser'));

/**
 * primary list page for groups
 */
Route::get('/groups', array('as'=>'ination.listgroup', 'uses'=> 'GroupsController@index'));

/**
 * create new group
 */
Route::post('/groups', array('as'=>'ination.newgroup', 'uses'=> 'GroupsController@create'));

/**
 * get the group info for the edit modal window
 */
Route::get('/groups/{group_id}', array('as'=>'ination.editgroup', 'uses'=> 'GroupsController@edit'));

/**
 * save a new group in the system
 */
Route::put('/groups/{group_id}', array('as'=>'ination.storegroup', 'uses'=> 'GroupsController@store'));

/**
 * removes a group from the system
 */
Route::delete('/groups/{group_id}', array('as'=>'ination.destroygroup', 'uses'=> 'GroupsController@destroy'));

//Ajax requests routes for inationsusers > for modal windows

/**
 * get the inationsuser that are not connected to the groups > add new inationsuser modal window
 */
Route::get('/groups/addinationsuser/{group_id}', array('as'=>'ination.usergroup', 'uses'=> 'GroupsController@addInationsuser'));

/**
 * save a new connection between group and inationsuser
 */
Route::put('/groups/addinationsuser/{group_id}/{user_id}', array('as'=>'ination.addusergroup', 'uses'=> 'GroupsController@storeInationsuser'));

/**
 * get the inationsuser that are connected to the group > remove inationsuser modal window
 */
Route::get('/groups/reminationsuser/{group_id}', array('as'=>'ination.remusergroup', 'uses'=> 'GroupsController@remInationsuser'));

/**
 * removes a connection between group and inationsuser
 */
Route::put('/groups/reminationsuser/{group_id}/{user_id}', array('as'=>'ination.removeusergroup', 'uses'=> 'GroupsController@destroyInationsuser'));