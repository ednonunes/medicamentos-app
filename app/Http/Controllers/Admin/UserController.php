<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        // Busca todos os usuários com seus medicamentos
        $users = User::with('medications')->get();
        return view('admin.users.index', compact('users'));
    }
}
