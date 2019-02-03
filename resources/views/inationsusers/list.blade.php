@extends('layouts.app')

@section('content')
   <div class="row mt40">
      <div class="col-md-10">
         @if ($canpressbuttons)
            <h2>iNations users management</h2>
         @else
            <h2>iNations users (view only)</h2>
         @endif
      </div>
      <div class="col-md-10">
         Search: <input type="text" class="input-search" alt="inationsusers_crud"  />
      </div>
      @if ($canpressbuttons)
         <div class="col-md-2 text-right">
            <button id="btn-add" name="btn-add" class="btn btn-primary btn-xs {{ $canpressbuttons }}">Add user</button>
         </div>
      @endif
      <br><br>
      <table class="table table-bordered table-striped table-hover" id="inationsusers_crud">
         <thead>
         <tr>
            <th>ID</th>
            <th>Name</th>
            <th>E-mail</th>
            <th>Obs</th>
            <th>Groups</th>
            @if ($canpressbuttons)
               <th colspan="2">Actions - Groups</th>
               <th colspan="2">Actions - Users</th>
            @endif
         </tr>
         </thead>
         <tbody id="inationsusers-list" name="inationsusers-list">
         @if (count($inationsusers) > 0)
            @foreach($inationsusers as $user)
               <tr id="inationsuser{{$user->id}}">
                  <td>{{ $user->id }}</td>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td>{{ $user->obs }}</td>

                  <td id="groups{{ $user->id }}" nowrap>
                     <strong>{!! nl2br(e( $user->groups)) !!}</strong>
                     <input type="hidden" id="userhasgroup{{ $user->id }}" value="{{ $user->hasgroups }}">
                  </td>

                  @if ($canpressbuttons)
                     <td>
                        <div class="col-md text-center">
                           <button class="btn btn-info btn-sm addgroup" value="{{ $user->id }}">Add</button>
                        </div>
                     </td>

                     <td>
                        <div class="col-md text-center">
                           <button class="btn btn-danger btn-sm remgroup" value="{{ $user->id }}">Remove</button>
                        </div>
                     </td>

                     <td>
                        <div class="col-md text-center">
                           <button class="btn btn-info btn-sm open-modal" value="{{ $user->id }}">Edit</button>
                        </div>
                     </td>

                     <td>
                        <div class="col-md text-center">
                           <button class="btn btn-danger btn-sm delete-inationsuser" value="{{ $user->id }}">Delete</button>
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

      <!-- modal window for new and edit user -->
      <div class="modal fade" id="inationsuserEditorModal" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="inationsuserEditorModalLabel"></h4>
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
                        <label for="name"  class="col-sm-2 control-label">E-mail</label>
                        <div class="col-sm-10">
                           <input type="email" class="form-control" id="email" name="email" placeholder="Insert e-mail" value="">
                        </div>
                     </div>

                     <div class="form-group">
                        <label for="obs" class="col-sm-2 control-label">Obs</label>
                        <div class="col-sm-10">
                           <textarea class="form-control" name="obs" id="obs" placeholder="Insert observations"></textarea>
                        </div>
                     </div>

                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary" id="btn-save" value="add">Save changes
                  </button>
                  <input type="hidden" id="user_id" name="user_id" value="0">
               </div>
            </div>
         </div>
      </div>

      <!-- modal window to confirm delete action -->
      <div class="modal fade" id="inationsuserConfirmModal" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="inationsuserConfirmModalLabel">Confirm action</h4>
               </div>
               <div class="modal-body">
                  <form id="modalFormData" name="modalFormData" class="form-horizontal" novalidate="">
                     {{ csrf_field() }}

                     <div class="form-group">
                        <div class="col-sm-12" id="form-confirmdata">
                           <!-- user related text -->
                        </div>
                     </div>

                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-danger delete-inationsuserconfirm" id="btn-confirmchange">Confirm</button>
                  <input type="hidden" id="userconfirm_id" name="userconfirm_id" value="0">
               </div>
            </div>
         </div>
      </div>

      <!-- > modal window for new and remove groups from users -->
      <div class="modal fade" id="groupEditorModal" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="groupEditorModalLabel"></h4>
               </div>
               <div class="modal-body">
                  <form id="modalFormData" name="modalFormData" class="form-horizontal" novalidate="">
                     {{ csrf_field() }}

                     <div id="form-errorsgroup"></div>

                     <div class="form-group">
                        <label for="group" class="col-sm-4 control-label">Select group</label>
                        <div class="col-sm-6">
                           <select id="group" name="group" class="form-control grouplist" required>
                           </select>
                        </div>
                     </div>

                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary" id="btn-savegroup" value="add">Save changes
                  </button>
                  <input type="hidden" id="group_user_id" name="group_user_id" value="0">
               </div>
            </div>
         </div>
      </div>
      {!! $inationsusers->links() !!}
   </div>

@endsection

@section('post-script')
   <script src="{{ asset('js/inationsusers.js') }}"></script>
   <script src="{{ asset('js/searchfield.js') }}"></script>
@endsection
