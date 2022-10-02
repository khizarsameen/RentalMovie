<div class="modal fade" id="movieModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                
            </div>
            <div class="modal-body bg-light">
                <div class="alert alert-danger city d-none"></div>
                <form id="movieForm">
                    <input type="hidden" id="movie_id" name="movie_id">

                    <div class="mb-2 row">
                        <label for="movie_name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control bg-white" id="movie_name" name="movie_name" >
                            <div class="invalid-feedback movie_name">

                            </div>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <label for="movie_descp" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-5">
                            <textarea class="form-control bg-white" id="movie_descp" name="movie_descp" rows="3"></textarea>
                            <div class="invalid-feedback movie_descp">

                            </div>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <label for="movie_cast" class="col-sm-2 col-form-label">Cast</label>
                        <div class="col-sm-5">
                            <textarea class="form-control bg-white" id="movie_cast" name="movie_cast" rows="3"></textarea>
                            <div class="invalid-feedback movie_cast">

                            </div>
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="movie_time" class="col-sm-2 col-form-label">Screen Time</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control bg-white" id="movie_time" name="movie_time">
                            
                            <div class="invalid-feedback movie_time">

                            </div>
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="movie_reldate" class="col-sm-2 col-form-label">Release Date</label>
                        <div class="col-sm-5">
                            <input type="date" class="form-control bg-white" id="movie_reldate" name="movie_reldate" value="{{ date('Y-m-d') }}">
                            
                            <div class="invalid-feedback movie_reldate">

                            </div>
                        </div>
                    </div>
                    
                    

                    
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="saveMovie">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>


            </div>
            </form>
        </div>
    </div>
</div>

@push('movieScripts')
<script type="text/javascript">
    $(document).ready(function() {
        

        


        $('#movieForm').submit(function(e) {
            e.preventDefault();

            
            
            $('#saveMovie').hide();
            $('#movieForm input').removeClass('is-invalid');
            $('.alert-danger.movie').fadeOut(function() {
                $(this).html("");
            });

            var fd = $(this).serialize();
            
            $.ajax({

                data: fd,
                url: "{{ route('movies.store')}}",
                cache: false,
                // processData: false,
                // contentType: false,
                type: 'POST',
                dataType: "json",
                success: function(data) {

                    $('#saveMovie').show();
                    
                    $('#movieForm').trigger('reset');
                    $('#movie_id').val("");
                    $('#movieForm input').removeClass('is-invalid');

                    
                    if ($('#saveMovie').val() == 'update') {
                        Swal.fire({
                                icon: 'success',
                                text: 'Movie updated successfully',
                                timer: 1400,
                                didClose: () => {
                                    $('#movieModal').modal('hide');
                                }
                            });            
            
                    } else {
                        Swal.fire({
                                icon: 'success',
                                text: 'Movie saved successfully',
                                timer: 1400
                                
                            });
                        
                    }
                    moviesTable.draw();



                },
                error: function(data) {
                    $('#saveMovie').show();
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