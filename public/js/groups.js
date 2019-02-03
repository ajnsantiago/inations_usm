$(document).ready(function () {

   //open the modal to add new group
   $('#btn-add').click(function () {
      $('#groupEditorModalLabel').html('Add group');
      $('#btn-save').val("add");
      $('#modalFormData').trigger("reset");
      $('#groupEditorModal').modal('show');
      $( '#form-errors' ).html('');
   });

   //open the modal to edit a group (fill modal info)
   $('body').on('click', '.open-modal', function () {
      var group_id = $(this).val();
      $('#groupEditorModalLabel').html('Edit group');
      $.get('groups/' + group_id, function (data) {
         $('#group_id').val(data.id);
         $('#name').val(data.name);
         $('#descr').val(data.descr);
         $('#btn-save').val("update");
         $( '#form-errors' ).html('');
         $('#groupEditorModal').modal('show');
      })
   });

   //open the modal to Add a user to a group (fill modal info)
   $('body').on('click', '.addinationsuser', function () {
      //remove options
      $('.inationsuserlist').children().remove();
      $('#btn-saveinationsuser').val("add");
      $('#form-errorsinations').html('');

      var group_id = $(this).val();
      $('#iuserEditorModalLabel').html('Add user');
      $.get('groups/addinationsuser/' + group_id, function (data) {
         $('#group_user_id').val(group_id);

         var gotInationsusers = false;
         $.each(data, function(key,val) {
            gotInationsusers = true;
            $('.inationsuserlist').append($('<option>', {
               value: val.id,
               text : val.name
            }));
         });

         if (!gotInationsusers) {
            $('.inationsuserlist').append($('<option>', {
               value: 0,
               text : ' --- '
            }));
         }

         $('#iuserEditorModal').modal('show');
      })
   });

   //open the modal to Remove a user from a group (fill modal info)
   $('body').on('click', '.reminationsuser', function () {
      //remove options
      $('.inationsuserlist').children().remove();
      $('#btn-saveinationsuser').val("rem");
      $( '#form-errorsinations' ).html('');
      $('#iuserEditorModalLabel').html('Remove user');

      var group_id = $(this).val();
      $.get('groups/reminationsuser/' + group_id, function (data) {
         $('#group_user_id').val(group_id);

         var gotInationsusers = false;
         $.each(data, function(key,val) {
            gotInationsusers = true;
            $('.inationsuserlist').append($('<option>', {
               value: val.id,
               text : val.name
            }));
         });

         if (!gotInationsusers) {
            //if the group does not have any user the process ends
            $("#inationsusers" + group_id).effect('highlight', {}, 500);
            return false;
         }

         $('#iuserEditorModal').modal('show');
      })
   });

   //open the modal to remove a group (fill modal info)
   $('body').on('click', '.delete-group', function () {
      if ($(this).hasClass('disabled')) {
         return false;
      }
      var group_id = $(this).val();
      $.get('groups/' + group_id, function (data) {
         $('#groupconfirm_id').val(data.id);$
         $('#form-confirmdata').html('Do you wish to remove the group: '+data.name+'?');
         $('#groupConfirmModal').modal('show');
      })
   });

   //called after confirmation of the deletion of the group > deletes a group
   $('body').on("click",".delete-groupconfirm", function(){
      var group_id = $('#groupconfirm_id').val();
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      $.ajax({
         type: "DELETE",
         url: 'groups/' + group_id,
         success: function (data) {
            $('#groupConfirmModal').modal('hide');
            $("#group" + group_id).hide('highlight', {}, 500);
         },
         error: function (data) {
            console.log('Error:', data);
         }
      });
   });

   // Clicking the save button on the open modal for both CREATE and UPDATE
   $("#btn-save").click(function (e) {
      if ($('#name').val() != '') {

         //set token csrf_token
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         e.preventDefault();
         var formData = {
            name: $('#name').val(),
            descr: $('#descr').val(),
         };
         var state = $('#btn-save').val();
         var type = "POST";
         var group_id = $('#group_id').val();
         var ajaxurl = 'groups';
         if (state == "update") {
            type = "PUT";
            ajaxurl = 'groups/' + group_id;
         }
         $.ajax({
            type: type,
            url: ajaxurl,
            data: formData,
            dataType: 'json',
            success: function (data) {
               var users = '';

               if (state != "add") {
                  users = $('#inationsusers' + group_id).html();
                  if (users == undefined || users == null || state == "add") {
                     users = '';
                  }
               }

               var curdescr = (data.descr == undefined || data.descr == null) ? '' : data.descr;

               var link = '<tr id="group' + data.id + '">' +
                  '<td>' + data.id + '</td>' +
                  '<td>' + data.name + '</td>' +
                  '<td>' + curdescr + '</td>' +
                  '<td id="inationsusers' + data.id + '">' + users;
               if (state == 'add') {
                  link += '<input type="hidden" id="grouphasuser' + data.id + '" value="0">';
               }
               link += '</td>';

               //buttons
               link += '<td>' +
                  '<div class="col-md text-center">' +
                  '<button class="btn btn-info btn-sm addinationsuser" value="' + data.id + '">Add</button>' +
                  '</div></td>';
               link += '<td>' +
                  '<div class="col-md text-center">' +
                  '<button class="btn btn-danger btn-sm reminationsuser" value="' + data.id + '">Remove</button>' +
                  '</div></td>';
               link += '<td>' +
                  '<div class="col-md text-center">' +
                  '<button class="btn btn-info btn-sm open-modal" value="' + data.id + '">Edit</button>' +
                  '</div></td>';

               var disdelbutton = '';
               if ($('#grouphasuser' + group_id).val() == 1) {
                  //block delete button (has users)
                  disdelbutton = 'disabled';
               }
               link += '<td>' +
                  '<div class="col-md text-center">' +
                  '<button class="btn btn-danger btn-sm delete-group '+ disdelbutton +'" value="' + data.id + '">Delete</button>' +
                  '</div></td></tr>';

               $('#groupEditorModal').modal('hide');
               if (state == "add") {
                  var numTR = $("#groups_crud > tbody").children().length - 1;
                  $('#groups-list').append(link);
                  $('#nodatafound').hide();

                  if (numTR / 10 == 1) {
                     location.reload();
                  }
               } else {
                  $("#group" + group_id).replaceWith(link);
                  $("#group" + group_id).effect('highlight', {}, 500);
               }

               $('#modalFormData').trigger("reset");
            },
            error: function (data) {
               console.log('Error:', data);
            }
         });
      } else {
         errorsHtml = '<div class="alert alert-danger"><ul>';
         errorsHtml += '<li>The name is mandatory</li>';
         errorsHtml += '</ul></di>';

         $('#form-errors').html(errorsHtml);
      }
   });

   // > salvar popup de novo user associado a grupo
   // save new user (or delete) to a group
   $("#btn-saveinationsuser").click(function (e) {
      if ($('#inationsuser').val() != 0) {
         //preparar token csrf_token
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         e.preventDefault();
         var formData = {
            inationsuser: $('#inationsuser').val(),
         };
         var state = $('#btn-saveinationsuser').val();
         var group_id = $('#group_user_id').val();

         var type = "PUT";
         var ajaxurl = 'groups/addinationsuser/' + group_id + '/' + $('#inationsuser').val();
         if (state == "rem") {
            ajaxurl = 'groups/reminationsuser/' + group_id + '/' + $('#inationsuser').val();
         }

         $.ajax({
            type: type,
            url: ajaxurl,
            data: formData,
            dataType: 'json',
            success: function (data) {
               if (state == "rem") {
                  //reload page to get the updated data
                  location.reload();
               } else {
                  //retrieve current groups html
                  var hasuser = $('#grouphasuser' + group_id).val();
                  $('#grouphasuser' + group_id).val(1);
                  var curhtml = $('#inationsusers' + group_id).html();
                  if (hasuser == 1) {
                     curhtml += '<br>';
                  }
                  $('#inationsusers' + group_id).html(
                     curhtml + '<strong>' + data.name + '</strong>'
                  );

                  $('#modalFormData').trigger("reset");
                  $('#iuserEditorModal').modal('hide');
                  $("#inationsusers" + group_id).effect('highlight', {}, 500);

                  //set disabled to delete button (can't delete the group)
                  $('#delgroupbutton' + group_id).addClass('disabled');
               }
            },
            error: function (data) {
               console.log('Error:', data);
            }
         });
      } else {
         var errormessage = 'Invalid user';
         errorsHtml = '<div class="alert alert-danger"><ul>';
         errorsHtml += '<li>'+errormessage+'</li>';
         errorsHtml += '</ul></di>';
         $('#form-errorsinations').html(errorsHtml);
      }
   });
});