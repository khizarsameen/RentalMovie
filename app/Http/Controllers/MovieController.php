<?php

namespace App\Http\Controllers;

use App\CustomMethods\LoggedUserFacade;
use App\Models\Movie;
use App\Models\UserMovie;
use App\Models\UserMovieRent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        if ($request->ajax()) {

            $data = DB::table('movies as a')

                ->select('a.id', 'a.name', 'a.screen_time', 'a.release_date');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<button class="btn btn-primary btn-sm btn-edit me-1" type="button" value="' . $row->id . '"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button class="btn btn-danger btn-sm btn-delete" type="button" value="' . $row->id . '"><i class="fa fa-trash"></i></button>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('movies');
    }

    public function listMovies(Request $request)
    {
        if ($request->ajax()) {
            $rentedMovies = DB::table('user_movies')
            ->where('exp_date', '>', date('Y-m-d H:i:s'))->get()->keyBy('movie_id');
            $data = DB::table('movies as a')

                ->select(
                    'a.id',
                    'a.name',
                    'a.screen_time',
                    'a.release_date',
                    
                );

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use($rentedMovies) {
                    $viewRoute = route('view.moviedetail', ['id' => $row->id]);
                    if($rentedMovies->get($row->id)){
                        $row->disabled = 'disabled';
                    }else{
                        $row->disabled = '';
                    }
                    $btn = '<a class="btn btn-primary btn-sm btn-view me-1" href="' . $viewRoute . '" >View Details</a>';
                    $btn .= '<button class="btn btn-primary btn-sm btn-view me-1 rent-btn" type="button" value="' . $row->id . '" ' . $row->disabled . '>Rent Movie</button>';

                    return $btn;
                })
                ->addColumn('status', function ($row) use($rentedMovies) {
                    if($row->disabled){
                        $status = 'Not Available';
                    }else{
                        $status = 'Available';

                    }
                    return $status;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'movie_name' => 'required|unique:movies,name,' . $request->movie_id,
            'movie_time' => 'required',
            'movie_reldate' => 'required',

        ];

        $messages = [
            'movie_name.required' => 'Movie Name must be entered',
            'movie_name.unique' => 'Movie Name must be unique',
            'movie_time.required' => 'Screen Time must be entered',
            'movie_reldate.required' => 'Release Date must be entered',



        ];

        Validator::make($request->all(), $rules, $messages)->validate();

        return DB::transaction(function () use ($request) {


            $data = [
                'name' => $request->movie_name, 'description' => $request->movie_descp, 'cast' => $request->movie_cast,
                'screen_time' => $request->movie_time, 'release_date' => $request->movie_reldate
            ];

            $rec = Movie::updateOrCreate(['id' => $request->movie_id], $data);

            return response()->json(['message' => 'success']);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function edit(Movie $movie)
    {
        return $movie;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movie $movie, Request $request)
    {

        if ($request->check == 'true') {
            $chk = UserMovie::where('movie_id', $movie->id)
                ->where('exp_date', '>', date('Y-m-d H:i:s'))->exists();

            if ($chk) {
                return response()->json(['errors' => ['error' => 'Rented Movies cannot be deleted']], 422);
            }

            $chk = UserMovie::where('movie_id', $movie->id)->exists();
            if ($chk) {
                return response()->json(['errors' => ['error' => 'Movie once rented cannot be deleted']], 422);
            }
        } else {
            Movie::destroy($movie->id);
        }
    }

    public function movieDetail($id)
    {
        $movie = Movie::findOrFail($id);
        $chk = UserMovie::where('movie_id', $movie->id)
        ->where('exp_date', '>', date('Y-m-d H:i:s'))->exists();

        if($chk) {
            $movie->rented = 1;
        }else{
            $movie->rented = 0;

        }
        return view('movie_details', compact('movie'));
    }

    public function rentMovie(Request $request)
    {

        

        $chk = UserMovie::where('movie_id', $request->movie_id)
            ->where('exp_date', '>', date('Y-m-d H:i:s'))->exists();

        if ($chk) {
            return response()->json(['errors' => ['error' => 'Movie is already rented']], 422);
        }
        $user_id = LoggedUserFacade::user()->id;
        $count = UserMovie::where('user_id', $user_id)->where('exp_date', '>', date('Y-m-d H:i:s'))->count();

        if ($count == 4) {
            return response()->json(['errors' => ['error' => 'User can rent 4 movies at a time']], 422);
        }


        $rules = [
            'days' => 'required|numeric|max:3|min:1',

        ];

        $messages = [
            'days.required' => 'No of days are required',
            'days.max' => 'Movie can only be rented for Maximum 3 days',
            'days.min' => 'Movie can only be rented for Minimum 1 day',



        ];

        Validator::make($request->all(), $rules, $messages)->validate();

        return DB::transaction(function () use ($request, $user_id) {


            date_default_timezone_set("Asia/Karachi");
            $days = $request->days;
            $expDate = date('Y-m-d H:i:s', strtotime("+{$days} days"));
            $data = [
                'user_id' => $user_id, 'movie_id' => $request->movie_id, 'date' => date('Y-m-d H:i:s'),
                'days' => $request->days, 'exp_date' => $expDate
            ];

            $dt = UserMovie::create($data);


            return response()->json('success');
        });
    }
}
