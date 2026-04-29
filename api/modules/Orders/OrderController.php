<?php

require_once 'core/Response.php';
require_once 'middlewares/AuthMiddleware.php';
require_once 'services/OrderService.php';

class OrderController {

    public function create() {
        $user = AuthMiddleware::handle();

        $orderId = OrderService::create($user['id']);

        if (!$orderId) {
            Response::json(['error'=>'empty cart'],400);
        }

        Response::json(['order_id'=>$orderId]);
    }
}

