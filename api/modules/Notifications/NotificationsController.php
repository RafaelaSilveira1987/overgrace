<?php

require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../services/notifications/NotificationService.php';

class NotificationsController
{
    public function list(): void
    {
        AuthMiddleware::handleAdmin();
        $unread = ($_GET['unread'] ?? '') === '1';
        Response::json(['success' => true] + NotificationService::listPanel(60, $unread));
    }

    public function read($id = null): void
    {
        AuthMiddleware::handleAdmin();
        NotificationService::markRead($id ? (int)$id : null);
        Response::json(['success' => true]);
    }

    public function process(): void
    {
        AuthMiddleware::handleAdmin();
        Response::json(['success' => true, 'result' => NotificationService::processPending(30)]);
    }
}
