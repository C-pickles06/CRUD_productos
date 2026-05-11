<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\Request;

class UserController extends Controller
{
    public function index(Request $request): void
    {
        $this->view('users/users', [
            'title' => 'Usuarios',
            'users' => User::all(),
        ]);
    }
}
