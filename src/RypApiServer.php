<?php

namespace Ryp\Utils;

class RypApiServer
{
    public static function ApiServer()
    {
        $env = getenv('APP_ENV');
        if($env == 'local'){
            $url = [
                'sso' => 'http://192.168.1.64:81',
                'goods' => 'http://192.168.1.188:8889',
                'message' => 'http://192.168.1.64:89',
                'wechat' => 'https://api.ecpei.cn/',
            ];
        }elseif($env == 'testing'){
            $url = [
                'sso' => 'http://192.168.1.188:81',
                'goods' => 'http://192.168.1.188:8889',
                'message' => 'http://192.168.1.188:89',
                'wechat' => 'https://api.ecpei.cn/',
            ];
        }elseif($env == 'aliyun_test'){
            $vision = '1-0-4.ecpei.cn';
            $url = [
                'sso' => 'https://u-api-test-'.$vision,
                'goods' => 'https://g-api-test-'.$vision,
                'message' => 'https://message-api-test-'.$vision,
                'wechat' => 'https://api.ecpei.cn/',
            ];
        }elseif($env == 'aliyun_test2'){
            $vision = '1-0-4.ecpei.cn';
            $url = [
                'sso' => 'https://u-api-test2-'.$vision,
                'goods' => 'https://g-api-test2-'.$vision,
                'message' => 'https://message-api-test2-'.$vision,
                'wechat' => 'https://api-test.ecpei.cn/',
            ];
        }else{
            $url = [
                'sso' => 'https://u-api.ecpei.cn',
                'goods' => 'https://g-api.ecpei.cn',
                'message' => 'https://message-api.ecpei.cn',
                'wechat' => 'https://api.ecpei.cn/',
            ];
        }

        return [
            'sso' => [
                'url' => $url['sso'],
                'api' => [
                    //获得手机验证码
                    'mobilecode' => '/api/user/mobilecode',
                    //验证token
                    'tokenVerify' => '/api/user/verifytoken',
                    //设置身份
                    'setidentity' => '/api/user/setidentity'
                ]
            ],
            'goods' => [
                'url' => $url['goods'],
                'api' => [
                    //主营品牌
                    'get_sseel_list' => '/api/app/ssell/getlist',
                    'address' => '/api/region/getregionname',

                    //es更新
                    'es' => '/api/es_create',
                ]
            ],

            'message' => [
                'url' => $url['message'],
                'api' => [
                    'send_message' => '/api/push/system/all',
                    //添加系统消息
                    'add_system_message' => '/api/push/system/add',
                    //app消息推送
                    'app_push' => '/api/push/app/add',

                    //socket
                    'socket_push' => '/api/push/socket/push',
                ]
            ],

            'wechat' => [
                'url' => $url['wechat'],
                'api' => [
                    'send_service_message' => 'api/other/wechat/message/send_service_message',
                ]
            ],
        ];
    }
}


