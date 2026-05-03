<?php

require_once 'api/core/Response.php';
require_once 'api/middleware/AuthMiddleware.php';
require_once 'api/services/CartService.php';

class CartController
{

    public function add()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $cartToken = $this->getCartToken();

        CartService::add(
            $cartToken,
            $data['produto_id'],
            $data['tamanho'],
            $data['quantidade'] ?? 1
        );

        Response::json(['success' => true]);
    }

    public function count()
    {
        $cartToken = $this->getCartToken();

        $total = CartService::countItems($cartToken);

        Response::json(['total' => $total]);
    }

    public function get()
    {
        $cartToken = $this->getCartToken();

        $cart = CartService::getCart($cartToken);

        Response::json($cart);
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $cartToken = $this->getCartToken();

        CartService::update(
            $cartToken,
            $id,
            $data['quantidade']
        );

        Response::json(['success' => true]);
    }

    public function applyCoupon()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            $cartToken = $this->getCartToken();
            $code = $data['cupom'] ?? null;

            $result = CartService::apply($cartToken, $code);

            Response::json($result);
        } catch (Exception $e) {

            Response::json([
                'success' => false,
                'mensagem' => $e->getMessage()
            ], 200); // importante: não usar 500 aqui
        }
    }


    public function delete($id)
    {
        $cart = CartService::delete($id);

        if (!$id) {
            Response::json(['error' => 'Item do carrinho não encontrado'], 404);
        }

        Response::json($cart);
    }

    private function getCartToken()
    {
        if (empty($_COOKIE['cart_token'])) {
            $token = bin2hex(random_bytes(16));

            setcookie(
                'cart_token',
                $token,
                time() + (60 * 60 * 24 * 30),
                '/',
                '',
                false,
                true
            );

            $_COOKIE['cart_token'] = $token; // importante para uso imediato
        }

        return $_COOKIE['cart_token'];
    }
}
