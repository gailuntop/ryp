<?php

namespace Ryp\Utils;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RypHandler extends ExceptionHandler
{

    use RypReturnJson;
    
    public function report(Exception $e)
    {
        if ($this->shouldntReport($e)) {
            //return;
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $e; // throw the original exception
        }
        
        self::sendSysLogRequest($e, 'text');
        $logger->error($e);

        $exception = $e;
        $status = $exception->getCode() > 0 ? $exception->getCode() : 1;
        if ( $exception instanceof NotFoundHttpException) {
            $message = "404找不到路由";
        }
        if ( $exception instanceof MethodNotAllowedHttpException) {
            $message = "请求方式错误";
        }
        if ( $exception instanceof FatalThrowableError) {
            $message = "致命错误：".$exception->getMessage();
        }
        if ( $exception instanceof ModelNotFoundException) {
            $message = "数据没有找到";
            $status = 101;
        }
        echo json_encode($this->returnInfo($status, $message ?? $exception->getMessage()));exit;
    }

    public static function sendSysLogRequest($e, $type = 'error')
    {
//        $url = env('APP_ENV') == 'local' ? 'http://192.168.1.64:82' : 'https://s-api-test-1-0-4.ecpei.cn';
//        $url .= '/log_action';
//        $data = [
//            'text' => $e,
//            'type' => $type,
//            'name' => $_SERVER['HTTP_HOST'] ?? 'ecpei',
//            'route_name' => \Request::getRequestUri()
//        ];
//        return Http::asyRequestCurl('POST', $url, $data);
    }
}