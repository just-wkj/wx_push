<?php

namespace app\controller;

use app\BaseController;
use app\service\WeChatService;
use think\facade\Config;
use think\facade\Request;

class Index extends BaseController {

    /**
     *  用户列表
     * @return \think\response\Json
     * @author: justwkj
     * @date: 2020/12/2 16:38
     */
    public function users() {
        $openIdMap = Config::get('openid.openIds');
        return $this->json(1, 'ok', [
            'users' => array_values($openIdMap),
        ]);
    }


    /**
     * 微信推送
     * @return \think\response\Json
     * @throws \think\Exception
     * @author: justwkj
     * @date: 2020/12/2 16:50
     */
    public function index() {
        $openIdMap = Config::get('openid.openIds');

        if (!Request::param('to')) {
            return $this->json(0, 'to invalid');
        }
        $users = explode(',', Request::param('to'));

        $title = Request::param('title');
        if (strlen($title) == 0) {
            return $this->json(0, 'title invalid');
        }
        $desc = Request::param('desc');
        if (strlen($desc) == 0) {
            $desc = $title;
        }

        $noticeSuccessUsers = [];
        foreach ($openIdMap as $openid => $nickname) {
            if (in_array($nickname, $users)) {
                WeChatService::pushMsg($openid, $title, $desc);
                $noticeSuccessUsers[] = $nickname;
            }
        }
        return $this->json(1, '已通知:' . implode(',', $noticeSuccessUsers));

    }

    /**
     *  简单的json封装
     * @param $code
     * @param string $msg
     * @param array $data
     * @return \think\response\Json
     * @author: justwkj
     * @date: 2020/12/2 16:50
     */
    private function json($code, $msg = 'ok', $data = []) {
        return json([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ]);
    }


}
