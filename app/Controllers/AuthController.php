<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\Csrf;
use Core\Request;
use Core\Session;

class AuthController extends Controller
{
    public function showLogin(Request $request): void
    {
        if (Session::has('user_id')) {
            $this->redirect('/products');
        }

        $this->view('auth/login', [
            'title' => 'Iniciar sesión',
            'error' => Session::flash('error'),
            'old' => Session::flash('old_username'),
        ]);
    }

    public function login(Request $request): void
    {
        if (!Csrf::verify((string) $request->input(Csrf::FIELD))) {
            Session::flash('error', 'Token CSRF inválido. Intenta de nuevo.');
            $this->redirect('/login');
        }

        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');

        if ($username === '' || $password === '') {
            Session::flash('error', 'Usuario y contraseña son obligatorios.');
            Session::flash('old_username', $username);
            $this->redirect('/login');
        }

        $user = User::findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::flash('error', 'Credenciales incorrectas.');
            Session::flash('old_username', $username);
            $this->redirect('/login');
        }

        Session::regenerate();
        Session::set('user_id', (string) $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_username', $user['username']);

        $this->redirect('/products');
    }

    public function logout(Request $request): void
    {
        if (!Csrf::verify((string) $request->input(Csrf::FIELD))) {
            $this->redirect('/products');
        }

        Session::destroy();
        $this->redirect('/login');
    }
}
