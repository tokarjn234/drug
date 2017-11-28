<?php

namespace App\Lib;

class OAuth2Helper
{
    public static function initOauthServer() {
        static $server = null;

        if ($server !== null) {
            return $server;
        }

        $dsn      = sprintf('mysql:dbname=%s;host=%s', env('DB_DATABASE', 'forge'), env('DB_HOST', 'localhost'));
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');

        require_once(app_path('Lib/OAuth2/Autoloader.php'));
        \OAuth2\Autoloader::register();
        $storage = new \OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

        $server = new \OAuth2\Server($storage, [
            'access_lifetime' => config('api.API_ACCESS_TOKEN_LIFETIME'),
            'token_bearer_header_name' => 'DrugOrder'
        ]);

        $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
        $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));

        return $server;
    }
}