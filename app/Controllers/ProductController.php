<?php

namespace App\Controllers;

use App\Models\Product;
use Core\Controller;
use Core\Csrf;
use Core\Request;
use Core\Session;
use Core\Validator;

class ProductController extends Controller
{
    public function index(Request $request): void
    {
        $this->view('products/index', [
            'title' => 'Productos',
            'products' => Product::all(),
            'success' => Session::flash('success'),
            'error' => Session::flash('error'),
        ]);
    }

    public function create(Request $request): void
    {
        $this->view('products/create', [
            'title' => 'Nuevo producto',
            'errors' => Session::flash('errors') ? json_decode(Session::flash('errors') ?? '[]', true) : [],
            'old' => $_SESSION['_old'] ?? [],
        ]);
        unset($_SESSION['_old']);
    }

    public function store(Request $request): void
    {
        $this->verifyCsrf($request);

        $data = $this->extract($request);
        $validator = Validator::make($data, $this->rules());

        if ($validator->fails()) {
            $this->flashOldAndErrors($data, $validator->firstErrors());
            $this->redirect('/products/create');
        }

        Product::create($data);
        Session::flash('success', 'Producto creado correctamente.');
        $this->redirect('/products');
    }

    public function edit(Request $request, string $id): void
    {
        $product = Product::find($id);
        if (!$product) {
            $this->notFound();
        }

        $this->view('products/edit', [
            'title' => 'Editar producto',
            'product' => $product,
            'errors' => Session::flash('errors') ? json_decode(Session::flash('errors') ?? '[]', true) : [],
            'old' => $_SESSION['_old'] ?? [],
        ]);
        unset($_SESSION['_old']);
    }

    public function update(Request $request, string $id): void
    {
        $this->verifyCsrf($request);

        $product = Product::find($id);
        if (!$product) {
            $this->notFound();
        }

        $data = $this->extract($request);
        $validator = Validator::make($data, $this->rules());

        if ($validator->fails()) {
            $this->flashOldAndErrors($data, $validator->firstErrors());
            $this->redirect('/products/' . urlencode($id) . '/edit');
        }

        Product::update($id, $data);
        Session::flash('success', 'Producto actualizado correctamente.');
        $this->redirect('/products');
    }

    public function destroy(Request $request, string $id): void
    {
        $this->verifyCsrf($request);

        $product = Product::find($id);
        if (!$product) {
            $this->notFound();
        }

        Product::delete($id);
        Session::flash('success', 'Producto eliminado correctamente.');
        $this->redirect('/products');
    }

    private function extract(Request $request): array
    {
        return [
            'nombre' => trim((string) $request->input('nombre')),
            'descripcion' => trim((string) $request->input('descripcion')),
            'precio' => $request->input('precio'),
            'stock' => $request->input('stock'),
        ];
    }

    private function rules(): array
    {
        return [
            'nombre' => 'required|min:2|max:150',
            'descripcion' => 'max:1000',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ];
    }

    private function verifyCsrf(Request $request): void
    {
        if (!Csrf::verify((string) $request->input(Csrf::FIELD))) {
            Session::flash('error', 'Token CSRF inválido.');
            $this->redirect('/products');
        }
    }

    private function flashOldAndErrors(array $data, array $errors): void
    {
        $_SESSION['_old'] = $data;
        Session::flash('errors', json_encode($errors, JSON_UNESCAPED_UNICODE));
    }

    private function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404');
        exit;
    }
}
