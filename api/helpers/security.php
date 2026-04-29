<?php

class Security {

    public static function clean($data) {
        if (is_array($data)) {
            return array_map([self::class, 'clean'], $data);
        }

        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function int($value) {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    public static function float($value) {
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }
}
