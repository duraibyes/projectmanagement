<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="commonExampleModalLabel"> {{ $title }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="task_form">
            <div class="modal-body">
                @csrf
                <input type="hidden" name="id" value="{{$info->id ?? ''}}">
                <div class="form-group my-3">
                    <label for="">Project</label>
                    <div>
                        <select name="project_id" id="project_id" onchange="getCollabarator(this.value)" class="form-control" required>
                            <option value="">--select --</option>
                            @isset($projects)
                                @foreach ($projects as $item)
                                    <option value="{{$item->id}}" @if( isset( $info->project_id ) && $info->project_id == $item->id ) selected @endif>{{ $item->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                </div>
                <div class="form-group my-3">
                    <label for=""> Project Collabarators </label>
                    <div>
                        <select name="collabarator[]" id="collabarator" class="form-control select2-user" multiple required>
                            @isset($info->project->collabarators)
                                @foreach ($info->project->collabarators as $item)
                                    <option value="{{ $item->user_id }}"  @if( isset($info->collabarators) && in_array( $item->user_id, array_column( $info->collabarators->toArray(), 'user_id'))  ) selected @endif >{{ $item->user->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                </div>
                <div class="form-group my-3">
                    <label for="">Task Name</label>
                    <div>
                        <input type="text" name="task_name" value="{{$info->name ?? ''}}" id="task_name" class="form-control" required>
                    </div>
                </div>
                <div class="form-group my-3">
                    <label for="">Description</label>
                    <div>
                        <textarea name="description" id="description" class="form-control" cols="30" rows="3">{{$info->description ?? ''}}</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2-user').select2();

        $("#task_form").validate({
            ignore: ":hidden",
            rules: {
                project_name: {
                    required: true,
                    minlength: 3
                },

            },
            submitHandler: function(form) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('task.save')}}",
                    data: $(form).serialize(),
                    success: function() {
                        $('#commonModal').modal('hide');
                        dtTable.draw();
                    }
                });
                return false; // required to block normal submit since you used ajax
            }
        });
    });

    function getCollabarator(project_id) {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('task.get_project_collabarators') }}",
                type: 'POST',
                data: {
                    project_id: project_id
                },
                success: function(res) {
                    var html = '';
                    if( res ) {
                        res.map((item) => {
                            html += `<option value="${item.user_id}">${item.user.name}</option>`;
                        })

                        $('#collabarator').html(html);
                    }

                },
                error: function(xhr, err) {
                    if (xhr.status == 403) {
                        toastr.error(xhr.statusText, 'UnAuthorized Access');
                    }
                }
            });
    }
</script>
