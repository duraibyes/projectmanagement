@extends('admin.layouts.template')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header p-3 d-flex justify-content-between">
                <h5>
                    Tasks
                </h5>
                <button class="btn primary-btn" onclick="return addTaskModel('', {{$project_id ?? ''}})"> Add Task</button>
            </div>
            <div class="card-body table-responsive">
                <table id="project-table" class="table table-hover table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Created Date</th>
                            <th>Task</th>
                            <th>Project</th>
                            <th>Collabarators</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('add_on_script')
    <script>
        var from = '{{ $from ?? ''}}';
        var project_id = '{{ $project_id ?? ''}}';
        if( from == 'project' ){
            var url = "{{route('project.task', ['project_id' => $project_id])}}"
        } else {

            var url = "{{ route('task')}}";
        }
        console.log('form', from);
        var dtTable = $('#project-table').DataTable({

            processing: true,
            serverSide: true,
            type: 'POST',
            ajax: {
                "url": url,
                "data": function(d) {
                    d.status = $('select[name=filter_status]').val();
                }
            },

            columns: [
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'name',
                    name: 'name'
                },                
                {
                    data: 'project',
                    name: 'project'
                },
                {
                    data: 'collabarator',
                    name: 'collabarator'
                }, {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-right"></i>', // or '→'
                    previous: '<i class="fa fa-angle-left"></i>' // or '←' 
                }
            },
            "aaSorting": [],
            "pageLength": 25
        });

        function addTaskModel(id = '',  project_id = '') {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('task.add_edit') }}",
                type: 'POST',
                data: {
                    id: id,
                    project_id:project_id
                },
                success: function(res) {

                    $('#commonModal').html(res);
                    $('#commonModal').modal('show');
                },
                error: function(xhr, err) {
                    if (xhr.status == 403) {
                        toastr.error(xhr.statusText, 'UnAuthorized Access');
                    }
                }
            });
        }

        function changeStatus(status, id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('task.status') }}",
                type: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function(res) {
                    Swal.fire({
                        title: "Updated!",
                        text: res.message,
                        icon: "success",
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-success"
                        },
                        timer: 3000
                    });
                    dtTable.draw();
                },
                error: function(xhr, err) {
                    if (xhr.status == 403) {
                        toastr.error(xhr.statusText, 'UnAuthorized Access');
                    }
                }
            });

        }

        function deleteTask(id) {

            Swal.fire({
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, Delete it!",
                cancelButtonText: "No, return",
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function(result) {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: "{{ route('task.delete') }}",
                        type: 'POST',
                        data: {
                            id: id,
                            status: status
                        },
                        success: function(res) {
                            Swal.fire({
                                title: "Updated!",
                                text: res.message,
                                icon: "success",
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-success"
                                },
                                timer: 3000
                            });
                            dtTable.draw();
                        },
                        error: function(xhr, err) {
                            if (xhr.status == 403) {
                                toastr.error(xhr.statusText, 'UnAuthorized Access');
                            }
                        }
                    });
                }
            });

        }
    </script>
@endsection
