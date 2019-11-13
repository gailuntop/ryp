<?php
/**
 * Created by PhpStorm.
 * User: J
 * Date: 17-5-3
 * Time: 上午11:43
 * 工具类
 */

namespace Ryp\Utils;


class RypFunction
{
    /**
     * 获取api接口
     * @param $amount
     * @param $rate
     */
    public static function calculateAmount($amount, $rate)
    {
        if ($rate) {
            return intval(($rate * $amount + $amount) * 100) / 100;
        } else {
            return $amount;
        }
    }

     /**
     * @param $str
     * 二进制转换字符串
     */
    public static function BinToStr($str)
    {
        $arr = explode(' ', $str);
        foreach($arr as &$v){
            $v = pack("H".strlen(base_convert($v, 2, 16)), base_convert($v, 2, 16));
        }
        return join('', $arr);
    }
    
    /**
     * @param $str
     * 字符串转换二进制
     */
    public static function StrToBin($str)
    {
        //1.列出每个字符
        $arr = preg_split('/(?<!^)(?!$)/u', $str);
        //2.unpack字符
        foreach($arr as &$v){
            $temp = unpack('H*', $v); $v = base_convert($temp[1], 16, 2);
            unset($temp);
        }
        return join(' ',$arr);
    }

    /**
     *对象、数组过滤包含emoji字符串
     * @param $object
     * @return string
     */
    public static function objectFilterEmoji($object)
    {
        if (is_object($object)) {//对象操作过滤emoji
            $object = json_decode(json_encode($object),true);
            foreach ($object as $key => $item) {
                //非对象、数组返回原来信息
                $object[$key] = $item;
                if (is_string($item)) {//字符串过滤emoji
                    $object[$key] = self::filterEmoji($item);
                } elseif (is_array($item)) {//数组递归调用
                    $object[$key] = self::objectFilterEmoji($item);
                }
            }
            $object = (object)$object;
        } elseif (is_array($object)) {//数组操作过滤emoji
            foreach ($object as $key => $item) {
                //非对象、数组返回原来信息
                $object[$key] = $item;
                if (is_string($item)) {//字符串过滤emoji
                    $object[$key] = self::filterEmoji($item);
                } elseif (is_array($item)) {//数组递归调用
                    $object[$key] = self::objectFilterEmoji($item);
                }
            }
        }
        //非对象、数组返回原来信息
        return $object;
    }

    /**
     * 过滤emoji字符串
     * @param $str
     * @return string|string[]|null
     */
    public static function filterEmoji($str)
    {
//        $cleanText = '';
        $emoJis=array(
            '/[\x{1F600}-\x{1F64F}]/u',
            '/[\x{1F300}-\x{1F5FF}]/u',
            '/[\x{1F680}-\x{1F6FF}]/u',
            '/[\x{2600}-\x{26FF}]/u',
            '/[\x{2700}-\x{27BF}]/u'
        );
        foreach ($emoJis as $value){
            if (preg_match($value,$str)){
                return false;
            }
        }

        return true;
    }

}
