<?php
/**
 * 地图基础服务组件。
 *
 * @author    JulyXing <julyxing@163.com>
 * @copyright © 2017 JulyXing
 * @license   GPL-3.0+
 */

namespace utility;

class MapService
{
    // 百度地图             BD-09坐标系
    // 腾讯地图             GCJ-02坐标系
    // 高德地图             GCJ-02坐标系
    // 阿里地图             GCJ-02坐标系
    // 图灵地图             GCJ-02坐标系
    // Google 地图         WGS84坐标系

    const PI = 3.14159265358979324;
    const PI_A = self::PI * 3000.0 / 180.0;

    const a = 6378245.0;
    const ee = 0.00669342162296594323;

    /**
     * WGS84(地球坐标系)转GCJ-02(火星坐标系)
     *
     * @param $lat
     * @param $lng
     * @return array
     * @throws \Exception
     */
    public static function wgs84_to_gcj02($lat, $lng)
    {
        $result = self::checkGEO($lat, $lng);
        if (200 != $result['code']) {
            throw new \Exception($result['message']);
        }

        $dLat = self::transFormLat($lng - 105.0, $lat - 35.0);
        $dLon = self::transFormLng($lng - 105.0, $lat - 35.0);
        $radLat = $lat / 180.0 * self::PI;
        $magic = sin($radLat);
        $magic = 1 - self::ee * $magic * $magic;
        $sqrtMagic = sqrt($magic);
        $dLat = ($dLat * 180.0) / ((self::a * (1 - self::ee)) / ($magic * $sqrtMagic) * self::PI);
        $dLon = ($dLon * 180.0) / (self::a / $sqrtMagic * cos($radLat) * self::PI);
        $lat = $lat + $dLat;
        $lng = $lng + $dLon;

        return array(
            'lat' => $lat,
            'lng' => $lng
        );
    }

    /**
     * WGS84(地球坐标系)转BD-09(百度坐标系)
     *
     * @param $lat
     * @param $lng
     * @return array
     */
    public static function wgs84_to_bd09($lat, $lng)
    {
        $a_row = self::wgs84_to_gcj02($lat, $lng);
        $a_result = self::gcj02_to_bd09($a_row['lat'], $a_row['lng']);

        return $a_result;
    }

    /**
     * GCJ-02(火星坐标系)转BD-09(百度坐标系)
     *
     * @param $lat
     * @param $lng
     * @return array
     * @throws \Exception
     */
    public static function gcj02_to_bd09($lat, $lng)
    {
        $result = self::checkGEO($lat, $lng);
        if (200 != $result['code']) {
            throw new \Exception($result['message']);
        }
        $x = $lng;
        $y = $lat;
        $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * self::PI_A);
        $theta = atan2($y, $x) + 0.000003 * cos($x * self::PI);
        $lng = $z * cos($theta) + 0.0065;
        $lat = $z * sin($theta) + 0.006;

        return array(
            'lat' => $lat,
            'lng' => $lng
        );
    }

    /**
     * BD-09(百度坐标系)转GCJ-02(火星坐标系)
     *
     * @param $lat
     * @param $lng
     * @return array
     * @throws \Exception
     */
    public static function bd09_to_gcj02($lat, $lng)
    {
        $result = self::checkGEO($lat, $lng);
        if (200 != $result['code']) {
            throw new \Exception($result['message']);
        }
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * self::PI_A);
        $theta = atan2($y, $x) - 0.000003 * cos($x * self::PI);
        $lng = $z * cos($theta);
        $lat = $z * sin($theta);

        return array(
            'lat' => $lat,
            'lng' => $lng
        );
    }

    /**
     * 检查经纬度是否在中国范围。
     *
     * @param $lat
     * @param $lng
     * @return array
     */
    public static function checkGEO($lat, $lng)
    {
        $message = '';
        $code = 200;
        if ($lat < 3.866666 || $lat > 53.55) {
            $message = "$lat 纬度超出中国范围";
            $code = 403;
        }
        if (73.666666 > $lng || 135.041666 < $lng) {
            $message = "$lng 经度超过中国范围";
            $code = 403;
        }
        return array(
            'message' => $message,
            'code' => $code
        );
    }

    /**
     * 纬度转换。
     *
     * @param $x
     * @param $y
     * @return float
     */
    private static function transFormLat($x, $y)
    {
        $ret = -100.0 + 2.0 * $x + 3.0 * $y + 0.2 * $y * $y + 0.1 * $x * $y + 0.2 * sqrt(abs($x));
        $ret += (20.0 * sin(6.0 * $x * self::PI) + 20.0 * sin(2.0 * $x * self::PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($y * self::PI) + 40.0 * sin($y / 3.0 * self::PI)) * 2.0 / 3.0;
        $ret += (160.0 * sin($y / 12.0 * self::PI) + 320 * sin($y * self::PI / 30.0)) * 2.0 / 3.0;

        return $ret;
    }

    /**
     * 经度转换。
     *
     * @param $x
     * @param $y
     * @return float
     */
    private static function transFormLng($x, $y)
    {
        $ret = 300.0 + $x + 2.0 * $y + 0.1 * $x * $x + 0.1 * $x * $y + 0.1 * sqrt(abs($x));
        $ret += (20.0 * sin(6.0 * $x * self::PI) + 20.0 * sin(2.0 * $x * self::PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($x * self::PI) + 40.0 * sin($x / 3.0 * self::PI)) * 2.0 / 3.0;
        $ret += (150.0 * sin($x / 12.0 * self::PI) + 300.0 * sin($x / 30.0 * self::PI)) * 2.0 / 3.0;

        return $ret;
    }
}
