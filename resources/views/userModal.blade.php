<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body bg-light">
                <div class="alert alert-danger city d-none"></div>
                <form id="userForm">
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="mb-2 row">
                        <label for="user_name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control bg-white" id="user_name" name="user_name" autocomplete="name">
                            <div class="invalid-feedback user_name">

                            </div>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <label for="user_email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control bg-white" id="user_email" name="user_email" autocomplete="email">

                            <div class="invalid-feedback user_email">

                            </div>
                        </div>
                    </div>


                    <div class="mb-2 row">
                        <label for="user_password" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-5">
                            <input type="password" class="form-control bg-white" id="user_password" name="user_password" autocomplete="new-password">
                            <div class="text-muted password-det"><small>Leave Empty to retain old password</small></div>
                            <div class="invalid-feedback user_password">

                            </div>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-sm-4 offset-sm-2">

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="admin" id="admin" value="1" checked>
                                <label class="form-check-label" for="admin">Admin</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="admin" id="guest" value="0">
                                <label class="form-check-label" for="guest">Guest</label>
                            </div>
                        </div>
                    
                    </div>




            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="saveUser">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>


            </div>
            </form>
        </div>
    </div>
</div>

@push('userScripts')
<script type="text/javascript">
    $(document).ready(function() {





        $('#userForm').submit(function(e) {
            e.preventDefault();



            $('#saveUser').hide();
            $('#userForm input').removeClass('is-invalid');
            $('.alert-danger.user').fadeOut(function() {
                $(this).html("");
            });

            var fd = $(this).serialize();

            $.ajax({

                data: fd,
                url: "{{ route('admin-users.store')}}",
                cache: false,
                // processData: false,
                // contentType: false,
                type: 'POST',
                dataType: "json",
                success: function(data) {

                    $('#saveUser').show();

                    

                    $('#userForm').trigger('reset');
                    $('#user_id').val("");
                    $('#userForm input').removeClass('is-invalid');


                    if ($('#saveUser').val() == 'update') {
                        Swal.fire({
                            icon: 'success',
                            text: 'User updated successfully',
                            timer: 1400,
                            didClose: () => {
                                $('#userModal').modal('hide');
                            }
                        });

                    } else {
                        Swal.fire({
                            icon: 'success',
                            text: 'User saved successfully',
                            timer: 1400

                        });

                    }
                    usersTable.draw();



                },
                error: function(data) {
                    $('#saveUser').show();
                    var subdata = JSON.parse(data.responseText);
                    if (data.status === 422) {
                        jQuery.each(subdata.errors, function(key, value) {
                            $(`#${key}`).addClass('is-invalid');
                            $(`.invalid-feedback.${key}`).text(value);
                            $(`.invalid-feedback.${key}`).show();

                        });
                    } else if (data.status === 500) {
                        var err = "";
                        jQuery.each(subdata.errors, function(key, value) {
                            err = err + value + "</br>";
                        });

                        Swal.fire({
                            icon: 'error',
                            html: err
                        });
                    } else {
                        $('.alert-danger.movie').html(subdata.message);
                        $('.alert-danger.movie').fadeIn();

                    }



                }
            })



        });


    });
</script>
@endpush