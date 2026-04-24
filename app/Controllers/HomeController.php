<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Session;

class HomeController extends Controller
{
    public function index(Request $request): void
    {
        if (Session::has('user_id')) {
            $this->redirect('/products');
        }
        $this->redirect('/login');
    }
}
