<?php

namespace Tests\Controllers\RecordPreparation;

use Firebase\JWT\JWT;

class JwtHeaderTokenGenerator
{
    public static function generate(array $data)
    {
        $payload = [
            'iss' => env('JWT_SERVER'),
            'jti' => env('JWT_TOKEN_ID'),
            'iat' => time(),
            'data' => $data
        ];
        $key = base64_decode(env('JWT_KEY'));

        $token = JWT::encode($payload, $key);
        return ["HTTP_Authorization" => 'Bearer ' . $token];
    }
}
