<!DOCTYPE html>
<html>
<head>
    <title>todo App</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
    <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
    <style>
        .spacing {
            margin-right: 200px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">To-do List</h2>
    <br>
    <div>
        <input type="text" id="taskName"  placeholder="New Task" required>
        <button onclick="createTask()" class="btn btn-primary spacing">Add Task</button>
    
        <label>
            <input type="checkbox" id="showAllTasks"> Show All Tasks
        </label>
    </div>
    <br><br><br>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Task</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="taskTable">
        </tbody>
    </table>
    
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        fetchTasks();
    });

    function fetchTasks() {
        const type = $('#showAllTasks').is(':checked') ? 'all' : '';
        $.ajax({
            url: '/tasks-list',
            type: 'GET',
            data: {type:type},
            success: function(response) {
                $('#taskTable').empty();
                response.forEach(task => {
                    const doneButton = task.status ? '' : `<button class="btn btn-success" onclick="updateTask(${task.id})">Done</button>`;
                    $('#taskTable').append(`
                        <tr>
                            <td>${task.id}</td>
                            <td>${task.name}</td>
                            <td>${task.status ? 'completed' : 'Pending'}</td>
                            <td>
                                ${doneButton}
                                <button class="btn btn-danger" onclick="deleteTask(${task.id})">Delete</button>
                           </td>
                        </tr>
                    `);
                });
            }
        });
    }
    $(document).ready(function() {
            fetchTasks();

            $('#showAllTasks').change(function() {
                fetchTasks();
            });
        });
    function createTask() {
        const taskName = $('#taskName').val();

            if (taskName.trim() === '') {
                alert('Task name is required');
                return;
            }
        $.ajax({
            url: '/tasks',
            type: 'POST',
            data: {
                name: taskName,
                status: 0
            },
            success: function(response) {
                iziToast.success({
                message: response.success,
                position: 'topCenter'
                 });
                fetchTasks();
                $('#taskName').val('');
            },
            error: function(xhr, status, error) {
            let errorMessage = 'An error occurred while creating the task.';
            if (xhr.status === 409) {
                errorMessage = xhr.responseJSON.error || 'Task with this name already exists.';
            }

            iziToast.error({
                message: errorMessage,
                position: 'topCenter'
            });
            fetchTasks();
            $('#taskName').val('');
        }
        });
    }

    function updateTask(id) {
        $.ajax({
            url: '/tasks/'+id,
            type: 'PUT',
            data: {
                name: 'Updated Task',
                status: 1
            },
            success: function(response) {
                iziToast.success({
                message: response.success,
                position: 'topCenter'
                 });
                fetchTasks();
            }
        });
    }
    function deleteTask(id) {
    if (confirm('Are you sure you want to delete this task?')) {
        $.ajax({
            url: '/tasks/'+id,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                iziToast.success({
                message: response.success,
                position: 'topCenter'
                 });
                fetchTasks();
            },
            error: function(xhr, status, error) {
                console.error('Error deleting task:', error);
            }
        });
    } else {
        console.log('Task deletion cancelled.');
    }
}
</script>
</body>
</html>