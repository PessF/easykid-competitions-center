<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    //
    public function index()
    {
        $userCount = User::count();
        $users = User::latest()->get(); 
        
        return view('admin.dashboard', compact('userCount', 'users'));
    }
}
