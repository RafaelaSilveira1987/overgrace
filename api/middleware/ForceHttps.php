<?php

class ForceHttps {

    public static function handle() {
        if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
            $url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location: $url");
            exit;
        }
    }
}
