<?php

use Illuminate\Support\Facades\Request;

if (!function_exists('route_is')) {
    /**
     * Mengecek apakah route saat ini cocok dengan salah satu dari nama route yang diberikan.
     *
     * @param  string|array  $routes
     * @return bool
     */
    function route_is($routes)
    {
        $routes = is_array($routes) ? $routes : [$routes];

        foreach ($routes as $route) {
            if (Request::routeIs($route)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('notify')) {
    /**
     * Membuat pesan notifikasi untuk alert.
     *
     * @param  string  $message
     * @param  string  $type
     * @return array
     */
    function notify($message, $type = 'success')
    {
        return [
            'message' => $message,
            'alert-type' => $type,
        ];
    }
}

if (!function_exists('alert')) {
    /**
     * Alias dari notify, untuk fleksibilitas penamaan.
     *
     * @param  string  $message
     * @param  string  $type
     * @return array
     */
    function alert($message, $type = 'success')
    {
        return [
            'message' => $message,
            'alert-type' => $type,
        ];
    }
}
