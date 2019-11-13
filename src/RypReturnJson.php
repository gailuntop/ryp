<?php

namespace Ryp\Utils;

trait RypReturnJson
{
    /**
     * 返回arr数据
     */
    public function returnInfo($status, $arr = '', $result = '')
    {
        if ($status) {
            $message = $arr;
            $data = empty($result) ? new \stdClass() : $result;
        } else {
            $status = 0;
            $message = $result ? $result : 'success';
            if (is_array($arr)) {
                $data = $arr;
            } else {
                $data = empty($arr) ? new \stdClass() : $arr;
            }
            
        }
        $data = [
            'status_code' => 200,
            'status' => (int)$status,
            'message' => (string)$message,
            'data' => $data
        ];
//        return response($data)
//            ->header('Content-Type', 'application/json')
//            ->cookie(cookie('key', 'ecpei'));
        return $data;
    }
    
}