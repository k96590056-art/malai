<?php
namespace App\Services;

class Lib
{
    /**
     * 获取ip地址
     */
    public static function getIpAddress($ip)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, sprintf('https://67ip.cn/check?ip=%s&token=%s', $ip, '53319c68fdda40a8b905d032bac04f45'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // 设置3秒超时
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // 设置2秒连接超时
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}