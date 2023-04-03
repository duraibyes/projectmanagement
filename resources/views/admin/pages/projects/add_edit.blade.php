<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="commonExampleModalLabel"> {{ $title }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="project_form">
            <div class="modal-body">
                @csrf
                <input type="hidden" name="id" value="{{$info->id ?? ''}}">
                <div class="form-group my-3">
                    <label for="">Project Name</label>
                    <div>
                        <input type="text" name="project_name" value="{{$info->name ?? ''}}" id="project_name" class="form-control" required>
                    </div>
                </div>
                <div class="form-group my-3">
                    <label for=""> Project Collabarators </label>
                    <div>
                        <select name="collabarator[]" id="collabarator" class="form-control select2-user" multiple>
                            @isset($users)
                                @foreach ($users as $item)
                                    <option value="{{ $item->id }}"  @if( isset($info->collabarators) && in_array( $item->id, array_column( $info->collabarators->toArray(), 'user_id'))  ) selected @endif >{{ $item->name }}</option>
                                @endforeach
                            @endisset
                        </select>
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

        $("#project_form").validate({
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
                    url: "{{ route('project.save')}}",
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
</script>
