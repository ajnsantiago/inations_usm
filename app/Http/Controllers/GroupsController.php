<?php

namespace App\Http\Controllers;

use App\Group;
use App\Inationsuser;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class GroupsController extends Controller
{
   /**
    * construct + validate if there is a login active
    *
    * GroupsController constructor.
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
      $data['groups'] = Group::paginate(10);

      //get all the inationsusers info that are connected to the groups
      foreach ($data['groups'] AS $k => $groups) {
         $inusers = '';

         if (isset($groups->inationsusers) && count($groups->inationsusers) > 0) {
            foreach ($groups->inationsusers AS $inationsusers) {
               if (strlen($inationsusers->name) > 0) {
                  if (strlen($inusers) > 0) {
                     $inusers .= "\n";
                  }
                  $inusers .= $inationsusers->name;
               }
            }
         }
         $groups['inationsusers'] = $inusers;
         if ($inusers == '') {
            $groups['hasusers'] = 0;
            $groups['disableDelButton'] = '';
         } else {
            $groups['hasusers'] = 1;
            $groups['disableDelButton'] = 'disabled';
         }

         $data['groups'][$k] = $groups;
      }
      //disable the action buttons if the person who is logged has no admin permissions
      $data['canpressbuttons'] = (Auth::user()->isadmin == 1) ? true : false;

      return view('groups.list',$data);
   }

   /**
    * create new group
    *
    * @param Request $request
    * @return mixed json
    */
   public function create(Request $request)
   {
      $request->validate([
         'name' => 'required|string|max:255'
      ]);

      $group = Group::create($request->all());
      return Response::json($group);
   }

   /**
    * get the group info for the edit modal window
    *
    * @param $group_id
    * @return mixed json
    */
   public function edit($group_id)
   {
      $group = Group::find($group_id);
      return Response::json($group);
   }

   /**
    * get the inationsuser that are not connected to the groups
    * add new inationsuser modal window
    *
    * @param $group_id
    * @return mixed json
    */
   public function addInationsuser($group_id)
   {
      $groups = Group::find($group_id);
      $inationsusersList = array();
      foreach ($groups->inationsusers as $inationsuser) {
         $inationsusersList[] = $inationsuser->pivot->inationsuser_id;
      }

      $inationsusers = Inationsuser::whereNotIn('id', $inationsusersList)->get();
      return Response::json($inationsusers);
   }

   /**
    * save a new connection between group and inationsuser
    *
    * @param $group_id
    * @param $user_id
    * @return mixed json
    */
   public function storeInationsuser($group_id, $user_id)
   {
      $group = Group::find($group_id);
      $group->inationsusers()->attach($user_id);

      $iuser = Inationsuser::find($user_id);
      return Response::json($iuser);
   }

   /**
    * get the inationsuser that are connected to the group
    * remove inationsuser modal window
    *
    * @param $group_id
    * @return mixed json
    */
   public function remInationsuser($group_id)
   {
      $groups = Group::find($group_id);
      $inationsusersList = array();
      foreach ($groups->inationsusers as $inationsuser) {
         $inationsusersList[] = $inationsuser->pivot->inationsuser_id;
      }

      $inationsusers = Inationsuser::whereIn('id', $inationsusersList)->get();
      return Response::json($inationsusers);
   }

   /**
    * removes a connection between group and inationsuser
    *
    * @param $group_id
    * @param $user_id
    * @return mixed json
    */
   public function destroyInationsuser($group_id, $user_id)
   {
      $group = Group::find($group_id);
      $group->inationsusers()->detach($user_id);

      $inationsusers = Inationsuser::find($user_id);
      return Response::json($inationsusers);
   }

   /**
    * save a new group in the system
    *
    * @param Request $request
    * @param $group_id
    * @return mixed json
    */
   public function store(Request $request, $group_id)
   {
      $group = Group::find($group_id);

      $request->validate([
         'name' => 'required|string|max:255'
      ]);

      $group->name = $request->name;
      $group->descr = $request->descr;
      $group->save();
      return Response::json($group);
   }

   /**
    * removes a group from the system
    *
    * @param $group_id
    * @return mixed json
    */
   public function destroy($group_id)
   {
      $group = Group::destroy($group_id);
      return Response::json($group);
   }
}
