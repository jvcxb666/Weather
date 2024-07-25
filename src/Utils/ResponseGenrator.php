<?php

namespace App\Utils;

use Exception;

class ResponseGenrator
{
    private const RESPONSE_STRUCTURE = [
        'status' => "success",
        "data" => [],
    ];

    public static function generate(mixed $content): array
    {
        $response = self::RESPONSE_STRUCTURE;
        if($content instanceof Exception) {
            $response['status'] = "error";
            $response['error'] = [
                "code" => $content->getCode(),
                "message" => $content->getMessage(),
            ];
        } else { 
            $response['data'] = $content;
        }

        return $response ?? [];
    }

}
