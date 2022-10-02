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
                <div class="card-header">{{ __('Movie Details') }}</div>

                <div class="card-body">
                    <div class="card mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <img src="{{ asset('images/movie_poster.png') }}" style="height: 100%;" class="img-fluid rounded-start" alt="...">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column">
                                    <input type="hidden" name="movie_id" id="movie_id" value="{{ $movie->id }}">
                                    <h5 class="card-title fw-bold">{{ $movie->name }}</h5>
                                    <span class="card-text">{{ $movie->description }}</span>

                                    <span class="card-text fw-bold"><small class="text-muted">Cast : {{ $movie->cast }}</small></span>
                                    <span class="card-text fw-bold"><small class="text-muted">Screen Time : {{ $movie->screen_time }}</small></span>
                                    <span class="card-text fw-bold"><small class="text-muted">Ratings : {{ $movie->ratings }}</small></span>
                                    <span class="card-text fw-bold mb-2"><small class="text-muted">Release Date : {{ date('d-m-Y', strtotime($movie->release_date)) }}</small></span>
                                    


                                    <div class="d-flex flex-row">
                                        <button class="btn btn-primary rent-btn me-2" type="button" <?= $movie->rented == 1 ? 'disabled' : ''?>>Rent This Movie</button>
                                        <a href="{{ route('home') }}" class="btn btn-success">Dashboard</a>
                                    </div>

                                </div>
                            </div>
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
    $('.rent-btn').click(function() {
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
                        movie_id: $('#movie_id').val()
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

                        
                            $('.rent-btn').attr('disabled', true);

                        



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