<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax()) {
            $data = User::latest()->get();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) {
                        $role = Auth::user()->roles()->first()->name;
                        $url = route('user.destroy', $row->id);
                        $btn = "<a href='javascript:void(0)' class='edit-user btn btn-success btn-sm' data-name='$row->name'
                        data-url='" . route('user.update', $row->id)."' data-email='$row->email' data-role='$role' data-toggle='modal' data-target='#editModal'>Edit</a>
                        <a href='$url' class='edit btn btn-danger btn-sm'>Delete</a>";

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
       return view('users');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request) {
    try{
        $user = User::create([
            'name'  =>  $request->name,
            'email' =>  $request->email,
            'password' => Hash::make($request->password)
        ]);
        $user->assignRole($request->role);
        toastr()->success('User created succesfully');

        return redirect()->back();
    } catch (Exception $e) {
        Log::error($e);
        toastr()->error($e->getMessage());
    }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {
    try{
        $user->update([
            'name'  =>  $request->name
        ]);
        $user->assignRole($request->role);
        toastr()->success('User created succesfully');

        return redirect()->back();
    } catch (Exception $e) {
        Log::error($e);
        toastr()->error($e->getMessage());
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        // $user->posts()->delete();
        $user->delete();

        toastr()->success('User deleted succesfully');
        return redirect()->back();
    }
}
