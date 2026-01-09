<?php

namespace App\Services;

class IpLocationService
{
    /**
     * Get location information from IP address
     * 
     * @param string $ip
     * @return string
     */
    public static function getLocation($ip)
    {
        if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
            return '本地';
        }

        try {
            // 使用免费的IP地址查询API
            $url = "http://ip-api.com/json/{$ip}?lang=zh-CN";
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if ($data && $data['status'] === 'success') {
                $country = $data['country'] ?? '';
                $region = $data['regionName'] ?? '';
                $city = $data['city'] ?? '';
                
                // 构建地区信息
                $location = '';
                if ($country) {
                    $location = $country;
                }
                if ($region && $region !== $country) {
                    $location .= $region;
                }
                if ($city && $city !== $region) {
                    $location .= $city;
                }
                
                return $location ?: '未知地区';
            }
        } catch (\Exception $e) {
            // 如果API调用失败，返回默认值
        }

        return '未知地区';
    }

    /**
     * Get location information with fallback to multiple APIs
     * 
     * @param string $ip
     * @return string
     */
    public static function getLocationWithFallback($ip)
    {
        if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
            return '本地';
        }

        // 尝试第一个API
        $location = self::getLocationFromIpApi($ip);
        if ($location !== '未知地区') {
            return $location;
        }

        // 尝试第二个API作为备用
        $location = self::getLocationFromIpInfo($ip);
        if ($location !== '未知地区') {
            return $location;
        }

        return '未知地区';
    }

    /**
     * Get location from ip-api.com
     */
    private static function getLocationFromIpApi($ip)
    {
        try {
            $url = "http://ip-api.com/json/{$ip}?lang=zh-CN";
            $response = file_get_contents($url, false, stream_context_create([
                'http' => [
                    'timeout' => 3
                ]
            ]));
            $data = json_decode($response, true);

            if ($data && $data['status'] === 'success') {
                $country = $data['country'] ?? '';
                $region = $data['regionName'] ?? '';
                $city = $data['city'] ?? '';
                
                $location = '';
                if ($country) {
                    $location = $country;
                }
                if ($region && $region !== $country) {
                    $location .= $region;
                }
                if ($city && $city !== $region) {
                    $location .= $city;
                }
                
                return $location ?: '未知地区';
            }
        } catch (\Exception $e) {
            // API调用失败
        }

        return '未知地区';
    }

    /**
     * Get location from ipinfo.io as fallback
     */
    private static function getLocationFromIpInfo($ip)
    {
        try {
            $url = "https://ipinfo.io/{$ip}/json";
            $response = file_get_contents($url, false, stream_context_create([
                'http' => [
                    'timeout' => 3
                ]
            ]));
            $data = json_decode($response, true);

            if ($data && !isset($data['error'])) {
                $country = $data['country'] ?? '';
                $region = $data['region'] ?? '';
                $city = $data['city'] ?? '';
                
                $location = '';
                if ($country) {
                    $location = $country;
                }
                if ($region && $region !== $country) {
                    $location .= $region;
                }
                if ($city && $city !== $region) {
                    $location .= $city;
                }
                
                return $location ?: '未知地区';
            }
        } catch (\Exception $e) {
            // API调用失败
        }

        return '未知地区';
    }
}

