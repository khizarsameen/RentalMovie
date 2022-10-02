@extends('layouts.app')
@section('css')




@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center mb-2">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('User Profile') }}</div>

                <div class="card-body">
                    <form id="userForm">
                        <div class="mb-2 row">
                            <label for="user_name" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control bg-white" id="user_name" name="user_name" autocomplete="name" value="{{ $user->name }}">
                                <div class="invalid-feedback user_name">

                                </div>
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <label for="user_email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control bg-white" id="user_email" name="user_email" autocomplete="email" value="{{ $user->email }}">

                                <div class="invalid-feedback user_email">

                                </div>
                            </div>
                        </div>


                        <div class="mb-2 row">
                            <label for="user_password" class="col-sm-2 col-form-label">Current Password</label>
                            <div class="col-sm-5">
                                <input type="password" class="form-control bg-white" id="user_cpassword" name="user_cpassword" autocomplete="new-password">
                                <div class="invalid-feedback user_cpassword">

                                </div>
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <label for="user_npassword" class="col-sm-2 col-form-label">New Password</label>
                            <div class="col-sm-5">
                                <input type="password" class="form-control bg-white" id="user_npassword" name="user_npassword" autocomplete="new-password">
                                <div class="invalid-feedback user_npassword">

                                </div>
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <div class="col-sm-4 offset-sm-2">
                                <button class="btn btn-primary" id="updateBtn">Update</button> 
                            <a href="{{ route('home') }}" class="btn btn-success">Dashboard</a>

                            </div>
                        </div>

                        


                </div>
                </form>

            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Rented Movies') }}</div>

                <div class="card-body">

                    <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">

                    <!-- <div class="row">
                        <div class="col-md-12"> -->
                    <table class="table table-sm table-bordered table-hover" style="width: 100%;" id="moviesTable">
                        <thead>
                            <tr>

                                <th style="width: 20%;">Name</th>
                                <th style="width: 20%;">Book Date</th>
                                <th style="width: 20%;">Duration</th>
                                <th style="width: 40%;">Expiry Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <!-- </div>
                    </div> -->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // window.moviesTable = undefined;
    var moviesTable;
</script>

<script>
    $(document).ready(function() {

        moviesTable = $('#moviesTable').DataTable({

            processing: false,
            serverSide: true,
            ajax: {
                "url": "{{ route('get.usermovies') }}",
                data: function(d) {

                    d.user_id = $('#user_id').val();

                }

            },
            columns: [{
                    data: 'name',
                    name: 'b.name',

                },
                {
                    data: 'date',
                    name: 'a.date',

                },
                {
                    data: 'days',
                    name: 'a.days',

                },
                {
                    data: 'exp_date',
                    name: 'a.exp_date',
                },


            ],




        });

    });

    $('#userForm').submit(function(e) {
            e.preventDefault();



            $('#updateBtn').hide();
            $('#userForm input').removeClass('is-invalid');
            $('.alert-danger.user').fadeOut(function() {
                $(this).html("");
            });

            var fd = $(this).serialize();
            fd += '&user_id='+$('#user_id').val();

            $.ajax({

                data: fd,
                url: "{{ route('update.guestprofile')}}",
                cache: false,
                // processData: false,
                // contentType: false,
                type: 'POST',
                dataType: "json",
                success: function(data) {

                    $('#updateBtn').show();

                    

                    $('#userForm').trigger('reset');
                    $('#userForm input').removeClass('is-invalid');
                    $('#user_cpassword').val('');
                    $('#user_npassword').val('');
                    Swal.fire({
                            icon: 'success',
                            text: 'User saved successfully',
                            timer: 1400

                        });



                },
                error: function(data) {
                    $('#updateBtn').show();
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
</script>
@endsection