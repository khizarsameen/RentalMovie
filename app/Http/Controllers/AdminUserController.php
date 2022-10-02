<?php

namespace App\Http\Controllers;

use App\CustomMethods\LoggedUserFacade;
use App\Models\User;
use App\Models\UserMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table('users as a')

                ->select('a.id', 'a.name', 'a.email');

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
        return view('admin_users');
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
            'user_name' => 'required',
            'user_email' => 'required|unique:users,email,' . $request->user_id,


        ];



        $messages = [
            'user_name.required' => 'Name must be entered',
            'user_email.required' => 'Email must be entered',
            'user_email.unique' => 'Email must be unique',
            'user_password.required' => 'Password must be entered',



        ];

        if (!($request->user_id > 0)) {
            $rules['user_password'] = 'required|string|min:8';
        }

        Validator::make($request->all(), $rules, $messages)->validate();

        return DB::transaction(function () use ($request) {

            if ($request->user_id > 0) {
                $data = [
                    'name' => $request->user_name,
                    'email' => $request->user_email,
                    'admin' => $request->admin,
                ];

                if ($request->user_password) {
                    $data['password'] = Hash::make($request->user_password);
                }

                User::where('id', $request->user_id)->update($data);
            } else {
                $data = [
                    'name' => $request->user_name,
                    'email' => $request->user_email,
                    'admin' => $request->admin,
                    'password' => Hash::make($request->user_password),
                ];

                User::create($data);
            }


            return response()->json(['message' => 'success']);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $admin_user)
    {
        return $admin_user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $admin_user, Request $request)
    {
        if ($request->check == 'true') {
            $chk = UserMovie::where('user_id', $admin_user->id)
                ->exists();

            if ($chk) {
                return response()->json(['errors' => ['error' => 'Entries exists under this user cannot be deleted']], 422);
            }

            $count = User::where('admin', 1)->count();
            if ($count == 1) {
                return response()->json(['errors' => ['error' => 'Atlease single admin user must be present']], 422);
            }
        } else {
            User::destroy($admin_user->id);
        }
    }

    public function guestProfile(Request $request)
    {
        $user = LoggedUserFacade::user();
        return view('guest_profile', compact('user'));
    }

    public function userMovies(Request $request)
    {
        $data = DB::table('user_movies as a')
            ->leftJoin('movies as b', function ($join) use ($request) {
                $join->on('a.movie_id', '=', 'b.id');
            })
            ->where('a.exp_date', '>', date('Y-m-d H:i:s'))
            ->where('a.user_id', $request->user_id)
            ->select('a.id', 'b.name', 'a.date', 'a.days', 'a.exp_date');

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function updateGuestProfile(Request $request){
        $rules = [
            'user_name' => 'required',
            'user_email' => 'required|unique:users,email,' . $request->user_id,
            'user_cpassword' => 'required',
            'user_npassword' => 'required',



        ];



        $messages = [
            'user_name.required' => 'Name must be entered',
            'user_email.required' => 'Email must be entered',
            'user_email.unique' => 'Email must be unique',
            'user_cpassword.required' => 'Current Password must be entered',
            'user_npassword.required' => 'New Password must be entered',



        ];

        

        Validator::make($request->all(), $rules, $messages)->validate();
        $user = User::findOrFail($request->user_id);
        if(!Hash::check($request->user_cpassword, $user->password)){
            return response()->json(['errors' => ['error' => 'Current Password is incorrect']], 500);
        }

        $data = [
            'name' => $request->user_name,
            'email' => $request->user_email,
            'password' => Hash::make($request->user_npassword),
        ];

        User::where('id', $request->user_id)->update($data);
        return response()->json('ok');
    }
}
