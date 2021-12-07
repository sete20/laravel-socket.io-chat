<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = \Auth::user()->friends;
        $groups = \Auth::user()->groups->where('status', 2);
        return view('home')->with(['users' => $users, 'groups' => $groups]);
    }
}
