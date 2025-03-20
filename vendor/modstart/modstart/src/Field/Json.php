<?php


namespace ModStart\Field;

use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\SerializeUtil;

/**
 * Json字段
 * {} 或 []
 *
 * Class Json
 * @package ModStart\Field
 */
class Json extends AbstractField
{
    const MODE_API = 'api';
    const MODE_DEFAULT = 'default';

    protected function setup()
    {
        $this->addVariables([
            'editorHeight' => '200px',
            // api | default
            'jsonMode' => 'default',
            'showMaxHeight' => '200px',
        ]);
    }

    /**
     * set data model
     * @param $value string api|default
     * @return $this
     */
    public function jsonMode($value)
    {
        $this->addVariables(['jsonMode' => $value]);
        return $this;
    }

    public function editorHeight($value)
    {
        $this->addVariables(['editorHeight' => $value]);
        return $this;
    }

    public function showMaxHeight($value)
    {
        if (is_numeric($value)) {
            $value = $value . 'px';
        }
        $this->addVariables(['showMaxHeight' => $value]);
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        return @json_decode($value, true);
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        $json = @json_decode($value, true);
        BizException::throwsIf($this->label . ' ' . L('Json Format Error'), $value && null === $json);
        return $json;
    }

    public static function executeApi($value)
    {
        // $value = [
        //     'url' => 'http://xxx.com',
        //     'method' => 'GET',
        //     'headers' => [],
        //     'query' => [],
        //     // FormData, UrlEncoded, Json
        //     'enctype' => 'Json',
        //     'bodyParam' => [],
        //     'bodyRaw' => '{}'
        //     'responseEnable'=> false,
        //     'responseType'=>'json',
        //     'responseJsonStatusPath'=>'code',
        //     'responseJsonStatusValue'=>'0',
        //     'responseJsonMsgPath'=>'msg',
        //     'responseValuePath'=>'data.value',
        // ];
        BizException::throwsIf('url为空', empty($value['url']));
        BizException::throwsIf('method错误', empty($value['method']) || !in_array($value['method'], ['GET', 'POST', 'PUT', 'DELETE']));
        BizException::throwsIf('enctype错误', empty($value['enctype']) || !in_array($value['enctype'], ['FormData', 'UrlEncoded', 'Json']));

        $param = [];
        $option = [];
        $option['returnHeader'] = true;
        $option['method'] = $value['method'];
        $option['header'] = [];
        if (!empty($value['headers'])) {
            foreach ($value['headers'] as $v) {
                $option['header'][$v['key']] = $v['value'];
            }
        }
        $option['query'] = [];
        if (!empty($value['query'])) {
            foreach ($value['query'] as $v) {
                $option['query'][$v['key']] = $v['value'];
            }
        }
        switch ($option['method']) {
            case 'POST':
                switch ($value['enctype']) {
                    case 'FormData':
                        $param = [];
                        if (!empty($value['bodyParam'])) {
                            foreach ($value['bodyParam'] as $v) {
                                $param[$v['key']] = $v['value'];
                            }
                        }
                        $option['header']['Content-Type'] = 'multipart/form-data';
                        break;
                    case 'UrlEncoded':
                        $param = [];
                        if (!empty($value['bodyParam'])) {
                            foreach ($value['bodyParam'] as $v) {
                                $param[$v['key']] = $v['value'];
                            }
                        }
                        $option['header']['Content-Type'] = 'application/x-www-form-urlencoded';
                        break;
                    case 'Json':
                        $param = $value['bodyRaw'];
                        $option['header']['Content-Type'] = 'application/json';
                        break;
                }
                break;
        }

        $ret = CurlUtil::request($value['url'], $param, $option);
        $resultData = [
            'success' => true,
            'body' => $ret['body'],
        ];
        if (empty($value['responseEnable'])) {
            $resultData['value'] = $resultData['body'];
            return Response::generateSuccessData($resultData);
        }

        if (200 != $ret['code']) {
            return Response::generateError('请求失败', [
                'raw' => $ret,
            ]);
        }

        if ('json' == $value['responseType']) {
            $json = @json_decode($ret['body'], true);
            if (empty($json)) {
                return Response::generateError('返回数据格式错误', [
                    'raw' => $ret
                ]);
            }
            $status = ArrayUtil::getByDotKey($json, $value['responseJsonStatusPath']);
            $msg = ArrayUtil::getByDotKey($json, $value['responseJsonMsgPath']);
            $responseJsonStatusValue = trim($value['responseJsonStatusValue']) . '';
            $resultData['value'] = ArrayUtil::getByDotKey($json, $value['responseValuePath']);
            if ('' !== $responseJsonStatusValue) {
                if ($responseJsonStatusValue != $status . '') {
                    $resultData['success'] = false;
                    $resultData['raw'] = $ret;
                }
            }
            return Response::generate(0, $msg, $resultData);
        }
        return Response::generateError('未知返回类型', [
            'raw' => $ret
        ]);
    }
}
