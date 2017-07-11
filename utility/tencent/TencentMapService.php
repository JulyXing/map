<?php
/**
 * 腾讯地图服务组件。
 *
 * @author    JulyXing <julyxing@163.com>
 * @copyright © 2017 JulyXing
 * @license   GPL-3.0+
 */

namespace utility\tencent;

use Exception;

class TencentMapService
{
    // 腾讯地图申请 key
    const KEY = '';

    const TRANSLATE_TYPE_GPS = 1;
    const TRANSLATE_TYPE_SOGOU = 2;
    const TRANSLATE_TYPE_BAIDU = 3;
    const TRANSLATE_TYPE_MAPBAR = 4;
    const TRANSLATE_TYPE_DEFAULT = 5;
    const TRANSLATE_TYPE_SOGOU_MERCATOR = 6;

    public function create()
    {
        return new TencentMapService();
    }

    /**
     * 坐标转换。
     *
     * @param $lat
     * @param $lng
     * @return
     */
    public static function translate($lat, $lng)
    {
        if (!$lat) {
            throw new Exception('缺少 lat');
        }
        if (!$lng) {
            throw new Exception('缺少 lng');
        }

        $params = array(
            'locations' => $lat . ',' . $lng,
            'type' => self::TRANSLATE_TYPE_GPS,
            'key' => self::KEY,
            'output' => 'json'
        );
        $url = TencentMapAPI::TRANSLATE . http_build_query($params);
        $data = Net::get($url);
        if (!isset($data)) {
            throw new Exception('坐标转换失败');
        }
        $result = Json::toArray($data);
        $codeResult = self::translateStatus($result['status']);
        if (0 != $result['status']) {
            throw new Exception($codeResult);
        }

        return $result['locations'][0];
    }

    /**
     * 坐标转换响应状态。
     *
     * @param $code
     * @return string
     */
    private static function translateStatus($code)
    {
        $errorCode = array(
            0 => '正常',
            310 => '请求参数信息错误',
            311 => 'key 格式错误',
            306 => '请求有护持信息请检查字符串',
            110 => '请求来源未被授权'
        );

        if (key_exists($code ,$errorCode)) {
            return $errorCode[$code];
        }

        return '未知错误';
    }

    /**
     * 逆地址解析。
     *
     * @param $lat
     * @param $lng
     * @return mixed
     */
    public static function geoCoder($lat, $lng)
    {
        if (!$lat) {
            throw new Exception('缺少 lat');
        }
        if (!$lng) {
            throw new Exception('缺少 lng');
        }

        $params = array(
            'location' => $lat . ',' . $lng,
            'type' => self::TRANSLATE_TYPE_GPS,
            'get_poi' => 0,
            'key' => self::KEY,
            'output' => 'json'
        );
        $url = TencentMapAPI::GEOCODER . http_build_query($params);
        $data = Net::get($url);
        if (!isset($data)) {
            throw new Exception('坐标转换失败');
        }
        $result = Json::toArray($data);
        $codeResult = self::translateStatus($result['status']);
        if (0 != $result['status']) {
            throw new Exception($codeResult);
        }

        return $result['result'];
    }

    public static function match($province, $city, $district = '')
    {
        // TODO
    }
}
