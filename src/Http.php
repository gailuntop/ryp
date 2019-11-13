<?php

namespace Ryp\Utils;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Log;

class Http
{
    public static function RypRedis()
    {
        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
        if ( getenv('REDIS_PASSWORD') != 'null' ) {
            $redis->auth(getenv('REDIS_PASSWORD'));
        }
        return $redis;
    }

    public static function getClient()
    {
        $client = new Client();
        return $client;
    }

    public static function get($url, $args = [], $otherArgs = [])
    {
        $args = array_merge($otherArgs, [
            'query' => array_merge($args, ['ecpei_key' => self::RypRedis()->get('internal_access_key')])
        ]);
        $client = self::getClient();
        $response = $client->request('GET', $url, $args);
        return \GuzzleHttp\json_decode($response->getBody());
    }

    public static function post($url, $args = [], $otherArgs = [])
    {
        $args = array_merge($otherArgs, [
            'form_params' => array_merge($args, ['ecpei_key' => self::RypRedis()->get('internal_access_key')])
        ]);
        $client = self::getClient();
        $response = $client->request('Post', $url, $args);
        return \GuzzleHttp\json_decode($response->getBody());
    }

    public static function requestAsync($method, $uri, $param)
    {
        //添加签名key
        $param = array_merge_recursive($param, [
            'form_params' => [
                'ecpei_key' => self::RypRedis()->get('internal_access_key'),
            ]
        ]);
        $curl = new CurlMultiHandler;
        $handler = HandlerStack::create($curl);
        $client = new Client([
            'handler' => $handler,
            'verify' => false,
        ]);
        $promise = $client->requestAsync($method, $uri, $param);
        $curl->tick();
        Log::info(
            "\r\n" .
            "===========进入异步调用==========\r\n" .
            "时间：".date('Y-m-d H:i:s') . "\r\n" .
            "请求地址:" . $uri  . "\r\n" .
            "请求方法:" . $method ."\r\n" .
            "请求参数:".json_encode($param, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * 异步调用
     * @param $url
     * @param array $param
     * @return mixed
     */
    public static function asyRequestCurl($method, $uri, $param, $version = 0)
    {
        $param = array_merge($param, ['ecpei_key' => self::RypRedis()->get('internal_access_key')]);
        $header = [];
        if ($version) {
            $header = [
                "Accept:".$version
            ];
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri); //1
        curl_setopt($ch, CURLOPT_HEADER, false); //2
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");//1
            if (count($param) != count($param, 1)) {
                $param = http_build_query($param);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);//1
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');//
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); // 头部
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);//
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //3

        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $tmpInfo;
    }
}