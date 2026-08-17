<?php

date_default_timezone_set('America/Sao_Paulo');
require_once dirname(__DIR__) . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
require_once dirname(__DIR__) . '/api/services/notifications/NotificationService.php';

$result = NotificationService::processPending(50);
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
