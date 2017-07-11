<?php
/**
 * 腾讯地图服务接口。
 *
 * @author    JulyXing <julyxing@163.com>
 * @copyright © 2017 JulyXing
 * @license   GPL-3.0+
 */

namespace utility\tencent;

class TencentMapAPI
{
    // 地点搜索
    const SEARCH = 'http://apis.map.qq.com/ws/place/v1/search?';

    // 关键词输入提示
    const SUGGEST = 'http://apis.map.qq.com/ws/place/v1/suggestion/?';

    // 逆地址解析
    const GEOCODER = 'http://apis.map.qq.com/ws/geocoder/v1/?';

    // 地址解析
    const ADDRESS = 'http://apis.map.qq.com/ws/geocoder/v1/?';

    // 坐标转换
    const TRANSLATE = 'https://apis.map.qq.com/ws/coord/v1/translate?';
}
