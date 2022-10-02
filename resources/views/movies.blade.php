@extends('layouts.app')
@section('css')




@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Movies') }}</div>

                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <button class="btn btn-primary " type="button" id="addbtn">Add Movie</button>
                            <a href="{{ route('home') }}" class="btn btn-success">Dashboard</a>


                        </div>


                        
                    </div>


                    <!-- <div class="row">
                        <div class="col-md-12"> -->
                    <table class="table table-sm table-bordered table-hover" style="width: 100%;" id="moviesTable">
                        <thead>
                            <tr>

                                <th style="width: 20%;">Name</th>
                                <th style="width: 20%;">Screen Time</th>
                                <th style="width: 20%;">Release Date</th>
                                <th style="width: 40%;">Action</th>
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
@include('movieModal')
@endsection

@section('js')
<script>
    // window.moviesTable = undefined;
    var moviesTable;

</script>
@stack('movieScripts')

<script>
    $(document).ready(function() {

        moviesTable = $('#moviesTable').DataTable({

            processing: false,
            serverSide: true,
            ajax: {
                "url": "{{ route('movies.index') }}",

            },
            columns: [
                {
                    data: 'name',
                    name: 'a.name',

                },
                {
                    data: 'screen_time',
                    name: 'a.screen_time',

                },
                {
                    data: 'release_date',
                    name: 'a.release_date',

                },
                {
                    data: 'action',
                    name: 'action',
                },


            ],




        });
        
    });

    

    
    


    $('#addbtn').click(function() {


        $('#movieModal .modal-title').text('Add Movie');
        $('#movieForm').trigger('reset');
        $('#movie_id').val('');
        $('#movieForm input').removeClass('is-invalid');
        $('#saveMovie').val('save');
        
        $('#movieModal').modal('show');
        $('#movieModal').on('shown.bs.modal', function() {
            $('#movie_name').trigger('focus');

        });

    });

    $('body').on('click', '.btn-edit', function() {
        
        var rowid = $(this).closest('tr').index();
        var id = $(this).val();

        $.get("{{ route('movies.index') }}" + '/' + id + '/edit', function(data) {

            $('#movieModal .modal-title').text('Edit Movie');
            $('#movieForm').trigger('reset');
            $('#movie_id').val(id);
            $('#movie_name').val(data.name);
            $('#movie_descp').val(data.description);
            $('#movie_cast').val(data.cast);
            $('#movie_time').val(data.screen_time);
            $('#movie_reldate').val(data.release_date);
            

            $('#movieForm input').removeClass('is-invalid');
            $('#saveMovie').val('update');
            $('#movieModal').modal('show');
            $('#movieModal').on('shown.bs.modal', function() {
                $('#movie_name').trigger('focus');
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
                        url: "{{ route('movies.store')}}" + "/" + id,
                        data: {
                            'check': false
                        },
                        success: function(data) {
                            moviesTable.draw();


                            Swal.fire({
                                icon: 'success',
                                text: 'Movie has been deleted.',
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
                url: "{{ route('movies.store')}}" + "/" + id,
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