<?php

require_once 'core/Response.php';
require_once 'middlewares/AuthMiddleware.php';
require_once 'services/CartService.php';

class CartController {

    public function add() {
        $user = AuthMiddleware::handle();

        $data = json_decode(file_get_contents("php://input"), true);

        CartService::add($user['id'], $data['product_id'], $data['qty']);

        Response::json(['success'=>true]);
    }

    public function get() {
        $user = AuthMiddleware::handle();

        $cart = CartService::get($user['id']);

        Response::json($cart);
    }
}

