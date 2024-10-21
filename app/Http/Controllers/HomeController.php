<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Correctly import the User model

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

        $user_id = auth()->user()->id;

        $user = User::find($user_id);

        if (!$user) {
            return redirect()->route('login')->withErrors('User not found.');
        }

        return view('home')->with('posts', $user->posts);
    }
}
