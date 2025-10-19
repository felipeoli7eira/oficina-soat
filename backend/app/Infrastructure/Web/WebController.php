<?php

declare(strict_types=1);

namespace App\Infrastructure\Web;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class WebController
{
    public ?Throwable $err = null;

    public function errResponse(string $msg, int $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR, ?array $additionalData = null)
    {
        $res = [
            'err' => true,
            'msg' => $msg
        ];

        if (is_array($additionalData) && count($additionalData)) {
            $res = array_merge($res, $additionalData);
        }

        if (app()->isLocal() && is_null($this->err) === false) {
            $res['err_trace'] = [
                'message' => $this->err->getMessage(),
                'file'    => $this->err->getFile(),
                'line'    => $this->err->getLine(),
                'trace'   => $this->err->getTrace(),
            ];
        }

        return response()->json($res, $httpCode);
    }

    public function successResponse(string $msg, int $httpCode = Response::HTTP_OK, ?array $additionalData = null)
    {
        $res = [
            'err' => false,
            'msg' => $msg
        ];

        if (is_array($additionalData) && count($additionalData)) {
            $res = array_merge($res, $additionalData);
        }

        if (app()->isLocal() && is_null($this->err) === false) {
            $res['err_trace'] = [
                'message' => $this->err->getMessage(),
                'file'    => $this->err->getFile(),
                'line'    => $this->err->getLine(),
                'trace'   => $this->err->getTrace(),
            ];
        }

        return response()->json($res, $httpCode);
    }

    public function useException(Throwable $err)
    {
        $this->err = $err;

        return $this;
    }
}
