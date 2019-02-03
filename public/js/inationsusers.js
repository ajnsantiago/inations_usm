$(document).ready(function () {

   //open the modal to add new user
   $('#btn-add').click(function () {
      $('#btn-save').val("add");
      $('#inationsuserEditorModalLabel').html('Add user');
      $('#modalFormData').trigger("reset");
      $('#inationsuserEditorModal').modal('show');
      $( '#form-errors' ).html('');
   });

   //open the modal to edit a user (fill modal info)
   $('body').on('click', '.open-modal', function () {
      var user_id = $(this).val();
      $('#inationsuserEditorModalLabel').html('Edit user');
      $.get('inationsusers/' + user_id, function (data) {
         $('#user_id').val(data.id);
         $('#name').val(data.name);
         $('#email').val(data.email);
         $('#obs').val(data.obs);
         $('#btn-save').val("update");
         $( '#form-errors' ).html('');
         $('#inationsuserEditorModal').modal('show');
      })
   });

   //open the modal to Add a group to a user (fill modal info)
   $('body').on('click', '.addgroup', function () {
      //remove options
      $('.grouplist').children().remove();
      $('#btn-savegroup').val("add");
      $( '#form-errorsgroup' ).html('');

      var user_id = $(this).val();
      $('#groupEditorModalLabel').html('Add group');
      $.get('inationsusers/addgroup/' + user_id, function (data) {
         $('#group_user_id').val(user_id);

         var gotGroups = false;
         $.each(data, function(key,val) {
            gotGroups = true;
            $('.grouplist').append($('<option>', {
               value: val.id,
               text : val.name
            }));
         });

         if (!gotGroups) {
            $('.grouplist').append($('<option>', {
               value: 0,
               text : ' --- '
            }));
         }

         $('#groupEditorModal').modal('show');
      })
   });

   //open the modal to Remove a group from a user (fill modal info)
   $('body').on('click', '.remgroup', function () {
      //remove options
      $('.grouplist').children().remove();
      $('#btn-savegroup').val("rem");
      $( '#form-errorsgroup' ).html('');

      var user_id = $(this).val();
      $('#groupEditorModalLabel').html('Remove group');
      $.get('inationsusers/remgroup/' + user_id, function (data) {
         $('#group_user_id').val(user_id);

         var gotGroups = false;
         $.each(data, function(key,val) {
            gotGroups = true;
            $('.grouplist').append($('<option>', {
               value: val.id,
               text : val.name
            }));
         });

         if (!gotGroups) {
            //if the user does not have any group the process ends
            $("#groups" + user_id).effect('highlight', {}, 500);
            return false;
         }

         $('#groupEditorModal').modal('show');
      })
   });

   //open the modal to remove a user (fill modal info)
   $('body').on('click', '.delete-inationsuser', function () {
      var user_id = $(this).val();
      $.get('inationsusers/' + user_id, function (data) {
         $('#userconfirm_id').val(data.id);
         $('#form-confirmdata').html('Do you wish to remove the user: '+data.name+'?');
         $('#inationsuserConfirmModal').modal('show');
      })
   });

   //called after confirmation of the deletion of the user > deletes a user
   $('body').on("click",".delete-inationsuserconfirm", function(){
      var user_id = $('#userconfirm_id').val();
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      $.ajax({
         type: "DELETE",
         url: 'inationsusers/' + user_id,
         success: function (data) {
            $('#inationsuserConfirmModal').modal('hide');
            $("#inationsuser" + user_id).hide('highlight', {}, 500);
         },
         error: function (data) {
            console.log('Error:', data);
         }
      });
   });

   // Clicking the save button on the open modal for both CREATE and UPDATE
   $("#btn-save").click(function (e) {
      if (($('#email').val() == '' ||
         ($('#email').val() != '' && validateEmail($('#email').val())))
         && $('#name').val() != ''
      ) {

         //set token csrf_token
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         e.preventDefault();
         var formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            obs: $('#obs').val(),
         };
         var state = $('#btn-save').val();
         var type = "POST";
         var user_id = $('#user_id').val();
         var ajaxurl = 'inationsusers';
         if (state == "update") {
            type = "PUT";
            ajaxurl = 'inationsusers/' + user_id;
         }
         $.ajax({
            type: type,
            url: ajaxurl,
            data: formData,
            dataType: 'json',
            success: function (data) {
               var groups = '';

               //retrieve current groups html
               if (state != "add") {
                  groups = $('#groups' + user_id).html();
                  if (groups == undefined || groups == null) {
                     groups = '';
                  }
               }

               var curobs = (data.obs == undefined || data.obs == null) ? '' : data.obs;
               var curemail = (data.email == undefined || data.email == null) ? '' : data.email;

               var link = '<tr id="inationsuser' + data.id + '">' +
                  '<td>' + data.id + '</td>' +
                  '<td>' + data.name + '</td>' +
                  '<td>' + curemail + '</td>' +
                  '<td>' + curobs + '</td>' +
                  '<td id="groups' + data.id + '">' + groups;
               if (state == 'add') {
                  link += '<input type="hidden" id="userhasgroup' + data.id + '" value="0">';
               }
               link += '</td>';

               //buttons
               link += '<td>' +
                  '<div class="col-md text-center">' +
                  '<button class="btn btn-info btn-sm addgroup" value="' + data.id + '">Add</button>' +
                  '</div></td>';
               link += '<td>' +
                  '<div class="col-md text-center">' +
                  '<button class="btn btn-danger btn-sm remgroup" value="' + data.id + '">Remove</button>' +
                  '</div></td>';
               link += '<td>' +
                  '<div class="col-md text-center">' +
                  '<button class="btn btn-info btn-sm open-modal" value="' + data.id + '">Edit</button>' +
                  '</div></td>';
               link += '<td>' +
                  '<div class="col-md text-center">' +
                  '<button class="btn btn-danger btn-sm delete-inationsuser" value="' + data.id + '">Delete</button>' +
                  '</div></td></tr>';

               if (state == "add") {
                  //check if reload is needed (for paginations to be shown)
                  var numTR = $("#inationsusers_crud > tbody").children().length - 1;
                  $('#inationsusers-list').append(link);
                  $('#nodatafound').hide();

                  if (numTR / 10 == 1) {
                     location.reload();
                  }
               } else {
                  $("#inationsuser" + user_id).replaceWith(link);
                  $("#inationsuser" + user_id).effect('highlight', {}, 500);
               }

               $('#modalFormData').trigger("reset");
               $('#inationsuserEditorModal').modal('hide')
            },
            error: function (data) {
               console.log('Error:', data);
            }
         });
      } else {
         var errormessage = 'Invalid E-mail';
         if ($('#name').val() == '') {
            errormessage = 'The name is mandatory';
         }
         errorsHtml = '<div class="alert alert-danger"><ul>';
         errorsHtml += '<li>'+errormessage+'</li>';
         errorsHtml += '</ul></di>';

         $('#form-errors').html(errorsHtml);
      }
   });

   // > save new group (or delete) to a user
   $("#btn-savegroup").click(function (e) {
      if ($('#group').val() != 0) {
         //set token csrf_token
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         e.preventDefault();
         var formData = {
            group: $('#group').val(),
         };
         var state = $('#btn-savegroup').val();
         var user_id = $('#group_user_id').val();

         var type = "PUT";
         var ajaxurl = 'inationsusers/addgroup/' + user_id + '/' + $('#group').val();
         if (state == "rem") {
            ajaxurl = 'inationsusers/remgroup/' + user_id + '/' + $('#group').val();
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
                  var hasgroup = $('#userhasgroup' + user_id).val();
                  $('#userhasgroup' + user_id).val(1);
                  var curhtml = $('#groups' + user_id).html();
                  if (hasgroup == 1) {
                     curhtml += '<br>';
                  }
                  $('#groups' + user_id).html(
                     curhtml + '<strong>' + data.name + '</strong>'
                  );

                  $('#modalFormData').trigger("reset");
                  $('#groupEditorModal').modal('hide');

                  $("#groups" + user_id).effect('highlight', {}, 500);
               }
            },
            error: function (data) {
               console.log('Error:', data);
            }
         });
      } else {
         var errormessage = 'Invalid group';
         errorsHtml = '<div class="alert alert-danger"><ul>';
         errorsHtml += '<li>'+errormessage+'</li>';
         errorsHtml += '</ul></di>';

         $('#form-errorsgroup').html(errorsHtml);
      }
   });
});

function validateEmail($email) {
   var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
   return emailReg.test( $email );
}