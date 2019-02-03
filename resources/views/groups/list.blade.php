@extends('layouts.app')

@section('content')
   <div class="row mt40">
      <div class="col-md-10">
         @if ($canpressbuttons)
            <h2>Groups management</h2>
         @else
            <h2>Groups (view only)</h2>
         @endif
      </div>

      <div class="col-md-10">
         Search: <input type="text" class="input-search" alt="groups_crud"  />
      </div>
      @if ($canpressbuttons)
         <div class="col-md-2  text-right">
            <button id="btn-add" name="btn-add" class="btn btn-primary btn-xs">Add group</button>
         </div>
      @endif
      <br><br>
      <table class="table table-bordered table-striped table-hover" id="groups_crud">
         <thead>
         <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Users</th>
            @if ($canpressbuttons)
               <th colspan="2">Actions - Users</th>
               <th colspan="2">Actions - Groups</th>
            @endif
         </tr>
         </thead>
         <tbody id="groups-list" name="groups-list">
         @if (count($groups) > 0)
            @foreach($groups as $group)
               <tr id="group{{$group->id}}">
                  <td>{{ $group->id }}</td>
                  <td>{{ $group->name }}</td>
                  <td>{{ $group->descr }}</td>

                  <td id="inationsusers{{ $group->id }}" nowrap>
                     <strong>{!! nl2br(e( $group->inationsusers)) !!}</strong>
                     <input type="hidden" id="grouphasuser{{ $group->id }}" value="{{ $group->hasusers }}">
                  </td>

                  @if ($canpressbuttons)
                     <td>
                        <div class="col-md text-center">
                           <button class="btn btn-info btn-sm addinationsuser" value="{{ $group->id }}">Add</button>
                        </div>
                     </td>
                     <td>
                        <div class="col-md text-center">
                           <button class="btn btn-danger btn-sm reminationsuser" value="{{ $group->id }}">Remove</button>
                        </div>
                     </td>

                     <td align="center">
                        <div class="col-md text-center">
                           <button class="btn btn-sm btn-info open-modal" value="{{ $group->id }}">Edit</button>
                        </div>
                     </td>
                        &nbsp;
                     <!-- delete active only if there is no users connected to the group -->
                     <td align="center">
                        <div class="col-md text-center">
                           <button id="delgroupbutton{{ $group->id }}" class="btn {{ $group->disableDelButton }} btn-sm btn-danger delete-group" value="{{ $group->id }}">Delete</button>
                        </div>
                     </td>
                  @endif
               </tr>
            @endforeach
         @else
            <tr id="nodatafound">
               <td colspan="9">
                  No records found
               </td>
            </tr>
         @endif
         </tbody>
      </table>

      <!-- modal window for new and edit group -->
      <div class="modal fade" id="groupEditorModal" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="groupEditorModalLabel"></h4>
               </div>
               <div class="modal-body">
                  <form id="modalFormData" name="modalFormData" class="form-horizontal" novalidate="">
                     {{ csrf_field() }}

                     <div id="form-errors">
                        <!-- show errors -->
                     </div>

                     <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10">
                           <input type="text" class="form-control" id="name" name="name" placeholder="Insert name" value="" required>
                        </div>
                     </div>

                     <div class="form-group">
                        <label for="descr" class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10">
                           <textarea class="form-control" name="descr" id="descr" placeholder="Insert description"></textarea>
                        </div>
                     </div>

                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary" id="btn-save" value="add">Save changes
                  </button>
                  <input type="hidden" id="group_id" name="group_id" value="0">
               </div>
            </div>
         </div>
      </div>

      <!-- modal window to confirm delete action -->
      <div class="modal fade" id="groupConfirmModal" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="groupConfirmModalLabel">Confirm action</h4>
               </div>
               <div class="modal-body">
                  <form id="modalFormData" name="modalFormData" class="form-horizontal" novalidate="">
                     {{ csrf_field() }}

                     <div class="form-group">
                        <div class="col-sm-12" id="form-confirmdata">
                           <!-- group related text -->
                        </div>
                     </div>

                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-danger delete-groupconfirm" id="btn-confirmchange">Confirm</button>
                  <input type="hidden" id="groupconfirm_id" name="groupconfirm_id" value="0">
               </div>
            </div>
         </div>
      </div>

      <!-- > modal window for new and remove users from groups -->
      <div class="modal fade" id="iuserEditorModal" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="iuserEditorModalLabel"></h4>
               </div>
               <div class="modal-body">
                  <form id="modalFormData" name="modalFormData" class="form-horizontal" novalidate="">
                     {{ csrf_field() }}

                     <div id="form-errorsinations"></div>

                     <div class="form-group">
                        <label for="inationsuser" class="col-sm-4 control-label">Select user</label>
                        <div class="col-sm-6">
                           <select id="inationsuser" name="inationsuser" class="form-control inationsuserlist" required>
                           </select>
                        </div>
                     </div>

                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary" id="btn-saveinationsuser" value="add">Save changes
                  </button>
                  <input type="hidden" id="group_user_id" name="group_user_id" value="0">
               </div>
            </div>
         </div>
      </div>
      {!! $groups->links() !!}
   </div>

@endsection

@section('post-script')
   <script src="{{ asset('js/groups.js') }}"></script>
   <script src="{{ asset('js/searchfield.js') }}"></script>
@endsection
