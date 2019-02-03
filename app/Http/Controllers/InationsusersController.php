<?php

namespace App\Http\Controllers;

use App\Inationsuser;
use App\Group;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class InationsusersController extends Controller
{

   /**
    * construct + validate if there is a login active
    *
    * InationsusersController constructor.
    */

   public function __construct()
   {
      $this->middleware('auth');
   }

   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $data['inationsusers'] = Inationsuser::paginate(10);

      //check all the groups associated with the inationsusers
      foreach ($data['inationsusers'] AS $k => $inationsusers) {
         $groups = '';
         //check associated groups
         if (isset($inationsusers->groups) && count($inationsusers->groups) > 0) {
            foreach ($inationsusers->groups AS $group) {
               error_log($group->name);
               if (strlen($group->name) > 0) {
                  if (strlen($groups) > 0) {
                     $groups .= "\n";
                  }
                  $groups .= $group->name;
               }
            }
         }
         $inationsusers['groups'] = $groups;
         $inationsusers['hasgroups'] = ($groups == '') ? 0 : 1;
         $data['inationsusers'][$k] = $inationsusers;
      }
      //disable the action buttons if the person who is logged has no admin permissions
      $data['canpressbuttons'] = (Auth::user()->isadmin == 1) ? true : false;
      return view('inationsusers.list',$data);
   }

   /**
    * create new inationsusers
    *
    * @param Request $request
    * @return mixed json
    */
   public function create(Request $request)
   {
      $request->validate([
         'name' => 'required|string|max:255'
      ]);

      $user = Inationsuser::create($request->all());
      return Response::json($user);
   }

   /**
    * get the inationsusers info for the edit modal window
    *
    * @param $user_id
    * @return mixed json
    */
   public function edit($user_id)
   {
      $user = Inationsuser::find($user_id);
      return Response::json($user);
   }

   /**
    * get the groups that are not connected to the inationsuser
    * add new group modal window
    *
    * @param $user_id
    * @return mixed json
    */
   public function addGroup($user_id)
   {
      $inationsusers = Inationsuser::find($user_id);
      $groupList = array();
      foreach ($inationsusers->groups as $group) {
         $groupList[] = $group->pivot->group_id;
      }

      $groups = Group::whereNotIn('id', $groupList)->get();
      return Response::json($groups);
   }

   /**
    * save a new connection between inationsuser and group
    *
    * @param $user_id
    * @param $group_id
    * @return mixed json
    */
   public function storeGroupUser($user_id, $group_id)
   {
      $user = Inationsuser::find($user_id);
      $user->groups()->attach($group_id);

      $group = Group::find($group_id);
      return Response::json($group);
   }

   /**
    * get the groups that are connected to the inationsuser
    * remove group modal window
    *
    * @param $user_id
    * @return mixed json
    */
   public function remGroup($user_id)
   {
      $inationsusers = Inationsuser::find($user_id);
      $groupList = array();
      foreach ($inationsusers->groups as $group) {
         $groupList[] = $group->pivot->group_id;
      }

      $groups = Group::whereIn('id', $groupList)->get();
      return Response::json($groups);
   }

   /**
    * removes a connection between inationsuser and group
    *
    * @param $user_id
    * @param $group_id
    * @return mixed json
    */
   public function destroyGroupUser($user_id, $group_id)
   {
      $user = Inationsuser::find($user_id);
      $user->groups()->detach($group_id);

      $group = Group::find($group_id);
      return Response::json($group);
   }

   /**
    * save a new inationsuser in the system
    *
    * @param Request $request
    * @param $user_id
    * @return mixed json
    */
   public function store(Request $request, $user_id)
   {
      $user = Inationsuser::find($user_id);

      $request->validate([
         'name' => 'required|string|max:255'
      ]);

      $user->name = $request->name;
      $user->email = $request->email;
      $user->obs = $request->obs;
      $user->save();
      return Response::json($user);
   }

   /**
    * removes a inationsuser from the system
    *
    * @param $user_id
    * @return mixed json
    */
   public function destroy($user_id)
   {
      //remove lines from pivot table
      Inationsuser::find($user_id)->groups()->newPivotStatement()->where('inationsuser_id', $user_id)->delete();

      $user = Inationsuser::destroy($user_id);
      return Response::json($user);
   }

}
