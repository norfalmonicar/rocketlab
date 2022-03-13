<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">

        <title>Task</title>

        <!-- STYLES -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css" />

        <!-- SCRIPTS -->
        <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <main class="container">
            <div class="my-3 p-3 bg-body rounded shadow-sm">
                <hr>
                <h1>TASKS</h1>
                <hr>
                <button type="button" class="btn btn-primary" id="btn_new_task"><i class="fa fa-tasks"></i> New Task</button>
                <button type="button" class="btn btn-primary" id="btn_new_user"><i class="fa fa-user fa-fw"></i> New User</button>
                <hr>
                <div>
                    <table id="dt-task" class="table table-striped table-bordered dataTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>TITLE</th>
                                <th>ASSIGNED TO</th>
                                <th>STATUS</th>
                                <th>PRIORITY</th>
                                <th>ESTIMATE</th>
                                <th>START/END</th>
                                <th>PROGRESS</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </main>

        <!-- Modal New Task -->
        <div class="modal fade" id="modal_newTask" tabindex="-1" aria-labelledby="newTaskLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newTaskLabel">New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="task_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="task_title" placeholder="Title">
                    </div>
                    <div class="mb-3">
                        <label for="task_estimate" class="form-label">Estimate</label>
                        <input type="number" class="form-control" id="task_estimate" placeholder="Estimate" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="task_started" class="form-label">Date Started and Ended</label>
                        <input type="text" id="task_datetimes" class="form-control"  />
                    </div>
                    <div class="mb-3">
                        <label for="task_status" class="form-label">Status</label>
                        <select class="form-select" id="task_status"> </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="create_task">Create</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Modal New User -->
        <div class="modal fade" id="modal_newUser" tabindex="-1" aria-labelledby="newUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newUserModalLabel">New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" placeholder="First Name">
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" placeholder="Last Name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="create_user">Create</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Modal Set Priority -->
        <div class="modal fade" id="modal_setPriority" tabindex="-1" aria-labelledby="setPriorityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setPriorityModalLabel">Set Priority</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="set_task_id" class="form-control"  />
                    <div class="mb-3">
                        <label for="task_priority" class="form-label">Priority</label>
                        <select class="form-select" id="task_priority"> </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save_priority">Save changes</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Modal Assigned To -->
        <div class="modal fade" id="modal_AssignedTo" tabindex="-1" aria-labelledby="AssignedToModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AssignedToModalLabel">Set Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="set_assigned_to" class="form-control"  />
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Assigned To</label>
                        <select class="form-select" id="user_id"> </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save_assignment">Save changes</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Title -->
        <div class="modal fade" id="modal_editTaskTitle" tabindex="-1" aria-labelledby="editTaskTitleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskTitleModalLabel">Edit Task Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_task_id" class="form-control"  />
                    <div class="mb-3">
                        <label for="edit_task_title" class="form-label">Task Title</label>
                        <input type="text" class="form-control" id="edit_task_title" placeholder="Task Title">
                    </div>
                    <div class="mb-3">
                        <label for="edit_task_estimate" class="form-label">Task Estimate</label>
                        <input type="text" class="form-control" id="edit_task_estimate" placeholder="Task Estimate">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="update_title">Save Changes</button>
                </div>
                </div>
            </div>
        </div>

    </body>
</html>

<script src="js/script.js"></script>
