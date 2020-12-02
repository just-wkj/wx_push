<?php
/**
 * @author: justwkj
 * @date: 2020/12/2 15:57
 * @email: justwkj@gmail.com
 * @desc:
 */

namespace app\service;


use think\Exception;
use think\facade\Cache;
use think\facade\Env;

class WeChatService {

    private static function getAccessToken() {
        $cacheKey = 'access_token';
        $accessToken = Cache::get($cacheKey);
        if (!$accessToken) {
            $accessToken = self::refreshAccessToken();
            if ($accessToken) {
                Cache::set($cacheKey, $accessToken, 7000);
            }
        }

        return $accessToken;
    }

    private static function refreshAccessToken() {
        $api = vsprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', [
            Env::get('WX_APP_ID'),
            Env::get('WX_APP_KEY'),
        ]);
        $result = \JustCurl::get($api);
        if ($result && $resultArr = json_decode($result, true)) {
            return $resultArr['access_token'];
        }
        return false;
    }

    public static function pushMsg($openid='okPkN5x_ZUUGdgNQyXRuQykQf09M',$title='',$msg='', $time=null) {
        if(!$time){
            $time = date('Y-m-d H:i:s');
        }

        $accessToken = self::getAccessToken();
        if(!$accessToken){
            throw  new Exception("token获取失败");
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;


        $data = [
            'touser' => $openid,
            'template_id' => 'd75JNLkL63yAxg48V4H63McmH68job3KyY5EWXf89Vw',
            'data' => [
                'title' => [
                    'value' => $title
                ],
                'time' => [
                    'value' => $time
                ],
                'msg' => [
                    'value' => $msg
                ]
            ]
        ];

       return \JustCurl::postJson($url, $data);

    }
}
