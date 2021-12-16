<?php

namespace Module\Vendor\Tecmz;

use Illuminate\Support\Facades\Log;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\SignUtil;

class Tecmz
{
    static $API_BASE = null;

    private $appId;
    private $appSecret;

    private $debug = false;

    public function __construct($appId, $appSecret = null)
    {
        self::$API_BASE = 'https://api.tecmz.com/open_api';
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * 校验签名
     *
     * @param $param
     * @return bool
     */
    public function signCheck($param)
    {
        if (empty($param['sign']) || empty($param['timestamp']) || empty($param['app_id'])) {
            return false;
        }
        if ($param['app_id'] != $this->appId) {
            return false;
        }
        if (($param['timestamp'] < time() - 1800 || $param['timestamp'] > time() + 1800)) {
            return false;
        }
        $sign = $param['sign'];
        unset($param['sign']);
        $signCalc = SignUtil::common($param, $this->appSecret);
        if ($sign != $signCalc) {
            return false;
        }
        return true;
    }

    /**
     * @param $appId
     * @param $appSecret
     * @return Tecmz
     */
    public static function instance($appId, $appSecret = null)
    {
        static $map = [];
        if (!isset($map[$appId])) {
            $map[$appId] = new self($appId, $appSecret);
        }
        return $map[$appId];
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    private function request($gate, $param = [])
    {
        $param['app_id'] = $this->appId;
        if ($this->appSecret) {
            $param['timestamp'] = time();
            $param['sign'] = SignUtil::common($param, $this->appSecret);
        }
        if ($this->debug) {
            Log::debug('SoftAPI -> ' . self::$API_BASE . $gate . ' -> ' . json_encode($param));
        }
        return CurlUtil::postJSONBody(self::$API_BASE . $gate, $param);
    }

    /**
     * 测试接口连通性
     * @return array
     *
     * 失败 [code=>-1,msg=>'<失败原因>',data=>null]
     * 成功 [code=>0,msg=>'ok',data=>null]
     */
    public function ping()
    {
        $ret = $this->request('/ping');
        if ($ret['code']) {
            return Response::generate(-1, 'PING失败');
        }
        return Response::generate(0, 'ok');
    }

    /**
     * 自助结算 创建订单
     *
     * @param $bizSn
     * @param $money
     * @param $notifyUrl
     * @param $returnUrl
     * @return array
     *
     * 失败 [code=>-1,msg=>'ok']
     * 成功 [code=>0,msg=>'ok','data'=>['biz_sn'=>'','sn'=>'','pay_url'=>'']]
     */
    public function payOfflineCreate($bizSn, $money, $notifyUrl, $returnUrl)
    {
        return $this->request('/pay_offline/create', [
            'biz_sn' => $bizSn,
            'money' => $money,
            'notify_url' => $notifyUrl,
            'return_url' => $returnUrl,
        ]);
    }

    /**
     * 安全验证获取验证信息
     *
     * @param $action
     * @param $key
     * @param $data
     * @param $runtime
     * @param $types
     * @return [code=>0,msg=>'ok',data=>[...]]
     */
    public function captchaVerify($action, $key, $data, $runtime, $types)
    {
        return $this->request('/captcha/verify', [
            'action' => $action,
            'key' => $key,
            'data' => $data,
            'runtime' => $runtime,
            'types' => $types,
        ]);
    }

    /**
     * @param $key
     * @return [code=>0,msg=>'ok'] 验证成功
     *         [code=>-1,msg=>'error'] 验证失败
     */
    public function captchaValidate($key)
    {
        return $this->request('/captcha/validate', [
            'key' => $key,
        ]);
    }

    /**
     * 短信服务 发送短信
     *
     * @param $phone
     * @param $templateId
     * @param array $params
     * @return array
     *
     * 失败 [code=>-1,msg=>'<失败原因>',data=>null]
     * 成功 [code=>0,msg=>'ok',data=>null]
     */
    public function smsSend($phone, $templateId, $params = [])
    {
        $post = [];
        foreach ($params as $k => $v) {
            $post["param_$k"] = $v;
        }
        return $this->request('/sms/send', array_merge([
            'phone' => $phone,
            'template_id' => $templateId,
        ], $post));
    }

    /**
     * 语音转换
     *
     * @param $from
     * @param $to
     * @param $contentBase64
     * @return array
     *
     * 失败 [code=>-1,msg=>'<失败原因>',data=>null]
     * 成功 [code=>0,msg=>'ok',data=>null]
     */
    public function audioConvert($from, $to, $contentBase64)
    {
        $post = [];
        $post['from'] = $from;
        $post['to'] = $to;
        $post['content'] = $contentBase64;
        return $this->request('/audio_convert', $post);
    }

    /**
     * ASR
     *
     * @param $type
     * @param $contentBase64
     * @return array
     *
     * 失败 [code=>-1,msg=>'<失败原因>',data=>null]
     * 成功 [code=>0,msg=>'ok',data=>null]
     */
    public function asr($type, $contentBase64)
    {
        $post = [];
        $post['type'] = $type;
        $post['content'] = $contentBase64;
        return $this->request('/asr', $post);
    }

    /**
     * 快递查询
     *
     * @param $type
     * @param $no
     * @return array
     *
     * 失败 [code=>-1,msg=>'<失败原因>',data=>null]
     * 成功 [code=>0,msg=>'ok',data=>null]
     */
    public function express($type, $no)
    {
        $post = [];
        $post['type'] = $type;
        $post['no'] = $no;
        return $this->request('/express', $post);
    }

}
