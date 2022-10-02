@extends('layouts.app')
@section('css')
<!-- <link href="{{ asset('fonts/alvi/stylesheet.css') }}" rel="stylesheet"> -->
<style>
    /* @import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu&family=Roboto:wght@100&display=swap'); */
    @font-face {
        font-family: alvi_Nastaleeq_Lahori_shipped;

        src: url('fonts/alvi_Nastaleeq_Lahori_shipped.ttf');
    }

    .urduText {
        /* font-family: 'noto_nastaliq_urduregular', serif !important; */
        font-family: alvi_Nastaleeq_Lahori_shipped !important;
        font-size: 34px;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-bordered table-hover" style="width: 100%;" id="moviesTable">
                                <thead>
                                    <tr>

                                        <th style="width: 20%;">S No</th>
                                        <th style="width: 20%;">Movie</th>
                                        <th style="width: 20%;">Status</th>
                                        <th style="width: 40%;">Action</th>

                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script>
    $(document).ready(function() {
        moviesTable = $('#moviesTable').DataTable({

            processing: false,
            serverSide: true,
            ajax: {
                "url": "{{ route('list.movies') }}",

            },
            columns: [
                {
                    data: 'id',
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'a.name',

                },
                {
                    data: 'status',
                    name: 'status',
                    // searchable: false


                },
                {
                    data: 'action',
                    name: 'action',

                },
                


            ],
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                $("td:first", nRow).html(iDisplayIndex + 1);
                return nRow;
            }




        });
    });

    $(document).on('click', '.rent-btn', function() {
        
        var movie_id = $(this).val();
        Swal.fire({
            title: 'Enter No of Days',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Rent',
            // showLoaderOnConfirm: true,
            // preConfirm: (login) => {
            //     console.log(login);
            // },
            // allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                
                $.ajax({

                    data: {
                        days: result.value,
                        movie_id: movie_id
                    },
                    url: "{{ route('rent.movies')}}",
                    cache: false,
                    // processData: false,
                    // contentType: false,
                    type: 'POST',
                    dataType: "json",
                    success: function(data) {
                        
                        Swal.fire({
                                icon: 'success',
                                text: 'Movie Rented successfully',
                                timer: 1400,
                            });

                        
                            moviesTable.draw();

                        



                    },
                    error: function(data) {
                        var subdata = JSON.parse(data.responseText);
                        var err = "";
                            jQuery.each(subdata.errors, function(key, value) {
                                err = err + value + "</br>";
                            });

                            Swal.fire({
                                icon: 'error',
                                html: err
                            });



                    }
                })
            }
        })
    });

    
</script>
@endsection