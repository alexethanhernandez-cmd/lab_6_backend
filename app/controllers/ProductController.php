<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->call->model('ProductModel', 'product');
        $this->call->library('api');
    }

    // GET - Display all products
    public function index()
    {
        $this->api->require_jwt();

        $products = $this->product->get_all_products();

        $this->api->respond([
            'status' => true,
            'data'   => $products
        ], 200);
    }

    // GET - Display one product
    public function show($id)
    {
        $this->api->require_jwt();

        $product = $this->product->get_product($id);

        if (!$product) {
            $this->api->respond_error('Product not found', 404);
        }

        $this->api->respond([
            'status' => true,
            'data'   => $product
        ], 200);
    }

    // POST - Add product
    public function store()
    {
        $this->api->require_jwt();

        $input = $this->api->body();

        if (
            empty($input['product_name']) ||
            !isset($input['price']) ||
            !isset($input['quantity'])
        ) {
            $this->api->respond_error(
                'Product name, price, and quantity are required.',
                400
            );
        }

        $data = [
            'product_name' => $input['product_name'],
            'description'  => $input['description'] ?? '',
            'price'        => $input['price'],
            'quantity'     => $input['quantity']
        ];

        $this->product->add_product($data);

        $this->api->respond([
            'status'  => true,
            'message' => 'Product added successfully.'
        ], 201);
    }

    // PUT/PATCH - Update product
    public function update($id)
    {
        $this->api->require_jwt();

        $input = $this->api->body();

        $product = $this->product->get_product($id);

        if (!$product) {
            $this->api->respond_error('Product not found', 404);
        }

        $data = [];

        if (isset($input['product_name'])) {
            $data['product_name'] = $input['product_name'];
        }

        if (isset($input['description'])) {
            $data['description'] = $input['description'];
        }

        if (isset($input['price'])) {
            $data['price'] = $input['price'];
        }

        if (isset($input['quantity'])) {
            $data['quantity'] = $input['quantity'];
        }

        if (empty($data)) {
            $this->api->respond_error('No product data supplied.', 400);
        }

        $this->product->update_product($id, $data);

        $this->api->respond([
            'status'  => true,
            'message' => 'Product updated successfully.'
        ], 200);
    }

    // DELETE - Delete product
    public function destroy($id)
    {
        $this->api->require_jwt();

        $product = $this->product->get_product($id);

        if (!$product) {
            $this->api->respond_error('Product not found', 404);
        }

        $this->product->delete_product($id);

        $this->api->respond([
            'status'  => true,
            'message' => 'Product deleted successfully.'
        ], 200);
    }
}