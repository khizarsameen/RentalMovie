@extends('layouts.app')
@section('css')




@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('User') }}</div>

                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <button class="btn btn-primary " type="button" id="addbtn">Add User</button>
                            <a href="{{ route('home') }}" class="btn btn-success">Dashboard</a>


                        </div>


                        
                    </div>


                    <!-- <div class="row">
                        <div class="col-md-12"> -->
                    <table class="table table-sm table-bordered table-hover" style="width: 100%;" id="usersTable">
                        <thead>
                            <tr>

                                <th style="width: 20%;">User Name</th>
                                <th style="width: 20%;">User Email</th>
                                <th style="width: 60%;">Action</th>
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
@include('userModal')
@endsection

@section('js')
<script>
    // window.moviesTable = undefined;
    var usersTable;

</script>
@stack('userScripts')

<script>
    $(document).ready(function() {

        usersTable = $('#usersTable').DataTable({

            processing: false,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin-users.index') }}",

            },
            columns: [
                {
                    data: 'name',
                    name: 'a.name',

                },
                {
                    data: 'email',
                    name: 'a.email',

                },
                
                {
                    data: 'action',
                    name: 'action',
                },


            ],




        });
        
    });

    

    
    


    $('#addbtn').click(function() {


        $('#userModal .modal-title').text('Add User');
        $('#userForm').trigger('reset');
        $('#user_id').val('');
        $('#userForm input').removeClass('is-invalid');
        $('#saveUser').val('save');
        $('.password-det').hide();
        $('#userModal').modal('show');
        $('#userModal').on('shown.bs.modal', function() {
            $('#user_name').trigger('focus');

        });

    });

    $('body').on('click', '.btn-edit', function() {
        
        var rowid = $(this).closest('tr').index();
        var id = $(this).val();
        console.log('ok');
        $.get("{{ route('admin-users.index') }}" + '/' + id + '/edit', function(data) {

            $('#movieModal .modal-title').text('Edit User');
            $('#userForm').trigger('reset');
            $('.password-det').show();

            $('#user_id').val(id);
            $('#user_name').val(data.name);
            $('#user_email').val(data.email);
            $(`input[name="admin"][value="${data.admin}"]`).prop('checked', true);
           
            

            $('#userForm input').removeClass('is-invalid');
            $('#saveUser').val('update');
            $('#userModal').modal('show');
            $('#userModal').on('shown.bs.modal', function() {
                $('#user_name').trigger('focus');
            });
        }).fail(function(data) {
            console.log(data);
        });


    });

    $('body').on('click', '.btn-delete', async function() {
        // if($('#pt_type :selected').text() != "General"){
        //     Swal.fire({
        //         icon: 'error',
        //         text: 'select General to delete procedure !',

        //     });
        //     return;
        // }
        var rowid = $(this).closest('tr').index();
        var id = $(this).val();

        try {
            var allowed = await deleteAllowed(id);

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('admin-users.store')}}" + "/" + id,
                        data: {
                            'check': false
                        },
                        success: function(data) {
                            usersTable.draw();


                            Swal.fire({
                                icon: 'success',
                                text: 'User has been deleted.',
                                timer: 1400,
                                // didClose: () => {
                                //     $('#zoneModal').modal('hide');
                                // }
                            });
                        },
                        error: function(data) {
                            var subdata = JSON.parse(data.responseText);
                            jQuery.each(subdata.errors, function(key, value) {


                            });


                        }
                    })

                }
            });

        } catch (error) {
            var error = JSON.parse(error.responseText);
            var errors = '';
            jQuery.each(error.errors, function(key, value) {
                errors += value;
            });
            Swal.fire({
                icon: 'error',
                html: errors
            });
        }





    });

    function deleteAllowed(id) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: "DELETE",
                url: "{{ route('admin-users.store')}}" + "/" + id,
                data: {
                    'check': true
                },
                success: function(data) {
                    resolve(data);

                },
                error: function(error) {
                    reject(error);
                }
            })
        });
    }
</script>
@endsection