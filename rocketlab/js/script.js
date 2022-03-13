
$(document).ready(function() {
    // DataTable
    $('#dt-task').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        ajax: "api/api.php?method=getListTask",
        columns: [{
                data: 'task_title' , orderable: false
            },
            {
                data: 'assigned_to' , orderable: true
            },
            { 
                data: 'task_status' , orderable: false
            },
            {
                data: 'task_priority' , orderable: true
            },
            {
                data: 'task_estimate' , orderable: false
            },
            {
                data: 'task_started' , orderable: false
            },
            { 'data': null, title: 'ACTION', wrap: true, "render": function (item) { 

                    var action = '<div> ';
                        
                        action += ' <button type="button" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Edit Title" onclick="edit_title(\'' + item.task_title + '\','  + item.task_id  + ',' + item.task_estimate +')"><i class="fa fa-edit fa-fw"></i></button>';

                        if( item.task_status != "Completed" ){
                            action += ' <button type="button" class="btn btn-info" data-toggle="tooltip" data-placement="top" title="Set Priority" onclick="set_priority(' + item.task_id + ')" ><i class="fa fa-cog fa-fw" aria-hidden="true"></i></button>';
                            action += ' <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Assigned To" onclick="assigned_to(' + item.task_id + ',' + item.user_id + ')" ><i class="fa fa-user fa-fw"></i></button>';
                            action += ' <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Complete Task" onclick="complete_task(' + item.task_id + ')" ><i class="fa fa-check" aria-hidden="true"></i></button>';        
                        }
                        action += ' <button type="button" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete Task" onclick="delete_task(' + item.task_id + ')"><i class="fa fa-trash-o fa-fw"></i></button>';

                        action += '</div>';

                    return action
                }
            },
        ],
        "aoColumnDefs": [{
            "bSortable": false,
            "aTargets": [0, 1, 2, 3, 4, 5, 6]
        }, ],
        "fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {

            var completed = oSettings.json.itotalCompleted;
            var text_task = "tasks";
            if( completed <= 1 ) { text_task = "task"; }

            return "Showing " + iStart + " - " + iEnd + " of " + iTotal + " entries with " + completed + " " + text_task + " completed";
        }
    });
});

//Passing of Task Title and Estimate to Modal and Opens the Modal
function edit_title( title, id, estimate ){
     
    $('#edit_task_id').val( id );
    $('#edit_task_title').val( title );
    $('#edit_task_estimate').val( estimate );
    
    $('#modal_editTaskTitle').modal('toggle');
}

//Updating the Task Title and Estimate
$('#update_title').click(function () {

    var estimate    = $('#edit_task_estimate').val().valueOf();

    if( $('#edit_task_title').val() == "" || estimate == "" ){
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Task Title and Task Estimate must not be empty!'
        })
    }else if( parseFloat(estimate) < 1 ){
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Task Estimate must not be a number and more than 0!'
        })
    }else{
        var data = {
            'task_title' : $('#edit_task_title').val(),
            'task_estimate' : estimate,
            'task_id' : $('#edit_task_id').val()
        };

        $.ajax({
            type: "POST",
            dataType: 'json',
            async: false,
            url: "api/api.php?method=updateTaskTitle",
            data: data,
            success: function (data) {
                if(data){
                    Swal.fire({
                        icon: 'success',
                        title: 'Task has been updated!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then((result) => {
                        location.reload();
                    })
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                    })
                }
            }
        });
    }

});

//Completing of the Task
function complete_task( id ){
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, complete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "get",
                dataType: 'json',
                async: false,
                url: "api/api.php?method=completeTask&task_id=" + id,
                success: function (data) {
                    if( data ){
                        Swal.fire({
                            icon: 'success',
                            title: 'Task has been completed!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then((result) => {
                            location.reload();
                        })
                    }else{
                        Swal.fire(
                            'Error!',
                            'Error in completing the task.',
                            'error'
                        )
                    }
                }
            });

        }
    })
}

//Passing of Task Priority to Modal and Opens the Modal
function set_priority( id ){
    $('#set_task_id').val( id );
    $.ajax({
        type: "get",
        dataType: 'json',
        async: false,
        url: "api/api.php?method=getListTaskPriority",
        success: function (data) {
            var html = "";
            $('#task_priority').html("");

            $.each(data, function (k, v) {
                html += "<option value=" + v.priority_id + ">" + v.priority_name + "</option>";
            });

            $('#task_priority').append(html);
        }
    });

    $('#modal_setPriority').modal('toggle');
}

//Deleting of the Task
function delete_task( id ){
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                type: "get",
                dataType: 'json',
                async: false,
                url: "api/api.php?method=deleteTask&task_id=" + id,
                success: function (data) {
                    if( data ){
                        Swal.fire({
                            icon: 'success',
                            title: 'Task has been deleted!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then((result) => {
                            location.reload();
                        })
                    }else{
                        Swal.fire(
                            'Error!',
                            'Error in deleting the task.',
                            'error'
                        )
                    }
                }
            });

        }
    })
}

//Passing of Assigned User ID to Modal and Opens the Modal
function assigned_to( id, user_id ){
    $('#set_assigned_to').val( id );
    $.ajax({
        type: "get",
        dataType: 'json',
        async: false,
        url: "api/api.php?method=getListUsers",
        success: function (data) {
            var html = "";
            $('#user_id').html("");

            $.each(data, function (k, v) {
                if( v.user_id == user_id ){
                    html += "<option value=" + v.user_id + " selected >" + v.first_name + ' ' + v.last_name + "</option>";
                }else{
                    html += "<option value=" + v.user_id + ">" + v.first_name + ' ' + v.last_name + "</option>";
                }
                
            });

            $('#user_id').append(html);
        }
    });

    $('#modal_AssignedTo').modal('show');
}

//Opens the New Task Modal
$('#btn_new_task').click(function () {
    $.ajax({
        type: "get",
        dataType: 'json',
        async: false,
        url: "api/api.php?method=getListTaskStatus",
        success: function (data) {
            var html = "";
            $('#task_status').html("");

            $.each(data, function (k, v) {
                html += "<option value=" + v.task_status_id + ">" + v.task_status_name + "</option>";
            });

            $('#task_status').append(html);
        }
    });

    $('#modal_newTask').modal('toggle');

});

//Opens the New User Modal
$('#btn_new_user').click(function () {

    $('#modal_newUser').modal('toggle');

});

//Saving of the New Task
$('#create_task').click(function () {

    var startDate   = $("#task_datetimes").data('daterangepicker').startDate.format('YYYY-MM-DD');
    var endDate     = $("#task_datetimes").data('daterangepicker').endDate.format('YYYY-MM-DD');
    var estimate    = $('#task_estimate').val().valueOf();

    if( $('#task_title').val() == "" || estimate == "" ){
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Task Title and Task Estimate must not be empty!'
        })
    }else if( parseFloat(estimate) < 1 ){
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Task Estimate must not be a number and more than 0!'
        })
    }else{
        var data = {
            'task_title' : $('#task_title').val(),
            'task_estimate' : estimate,
            'task_status' : $('#task_status').val(),
            'task_started' : startDate,
            'task_ended' : endDate
        };

        $.ajax({
            type: "POST",
            dataType: 'json',
            async: false,
            url: "api/api.php?method=insertTask",
            data: data,
            success: function (data) {
                if(data){
                    Swal.fire({
                        icon: 'success',
                        title: 'Task has been saved!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then((result) => {
                        location.reload();
                    })
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                    })
                }
            }
        });
    }

});

//Saving of the New Users
$('#create_user').click(function () {

    var data = {
        'first_name' : $('#first_name').val(),
        'last_name' : $('#last_name').val()
    };

    if( $('#first_name').val() == "" || $('#last_name').val() == ""){
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'First Name and Last Name must not be empty!'
        })
    }else{
        $.ajax({
            type: "POST",
            dataType: 'json',
            async: false,
            url: "api/api.php?method=insertUser",
            data: data,
            success: function (data) {
                if(data){
                    Swal.fire({
                        icon: 'success',
                        title: 'User has been saved!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then((result) => {
                        location.reload();
                    })
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                    })
                }
            }
        });
    }

});

//Saving of Updated Priority
$('#save_priority').click(function () {

    var data = {
        'task_id' : $('#set_task_id').val(),
        'task_priority' : $('#task_priority').val()
    };

    $.ajax({
        type: "POST",
        dataType: 'json',
        async: false,
        url: "api/api.php?method=updateTaskPriority",
        data: data,
        success: function (data) {
            if(data){
                Swal.fire({
                    icon: 'success',
                    title: 'Priority has been updated!',
                    showConfirmButton: false,
                    timer: 1500
                }).then((result) => {
                    location.reload();
                })
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!'
                })
            }
        }
    });

});

//Saving of Updated new Assignment
$('#save_assignment').click(function () {

    var data = {
        'task_id' : $('#set_assigned_to').val(),
        'user_id' : $('#user_id').val()
    };

    $.ajax({
        type: "POST",
        dataType: 'json',
        async: false,
        url: "api/api.php?method=updateTaskAssignment",
        data: data,
        success: function (data) {
            if(data){
                Swal.fire({
                    icon: 'success',
                    title: 'Task has been assigned!',
                    showConfirmButton: false,
                    timer: 1500
                }).then((result) => {
                    location.reload();
                })
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!'
                })
            }
        }
    });

});

//Setting of range date picker for Start and End Date
$(function() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();

    $('#task_datetimes').daterangepicker({
        timePicker: false,
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('hour').add(32, 'hour'),
        locale: {
            format: 'MMMM DD'
        },
        minDate: today
    });
});