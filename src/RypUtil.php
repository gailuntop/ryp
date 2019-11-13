<?php
/**
 * Created by PhpStorm.
 * User: J
 * Date: 17-5-3
 * Time: 上午11:43
 * 工具类
 */

namespace Ryp\Utils;


class RypUtil
{
    /**
     * 获取api接口
     * @param $service
     * @param $api
     * @return \Closure|string
     */
    public static function api($service, $api)
    {
        $url =  config('apiService.'.$service.'.url');
        $api = config('apiService.'.$service.'.api.'.$api);
        if ($api instanceof \Closure) {
            $apiUrl = function () use($url,$api) {
                $routeArgs = func_get_args();
                $api = call_user_func_array($api, $routeArgs);
                return $url.$api;
            };
        }else{
            $apiUrl = $url.$api;
        }
        return $apiUrl;
    }
}