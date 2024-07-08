<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithOIDCResponseBody;

use AlibabaCloud\Tea\Model;

class credentials extends Model
{
    /**
     * @description The AccessKey ID.
     *
     * @example STS.NUgYrLnoC37mZZCNnAbez****
     *
     * @var string
     */
    public $accessKeyId;

    /**
     * @description The AccessKey secret.
     *
     * @example CVwjCkNzTMupZ8NbTCxCBRq3K16jtcWFTJAyBEv2****
     *
     * @var string
     */
    public $accessKeySecret;

    /**
     * @description The time when the STS token expires. The time is displayed in UTC.
     *
     * @example 2021-10-20T04:27:09Z
     *
     * @var string
     */
    public $expiration;

    /**
     * @description The STS token.
     *
     * > Alibaba Cloud STS does not impose limits on the length of STS tokens. We strongly recommend that you do not specify a maximum length for STS tokens.
     * @example CAIShwJ1q6Ft5B2yfSjIr5bSEsj4g7BihPWGWHz****
     *
     * @var string
     */
    public $securityToken;
    protected $_name = [
        'accessKeyId'     => 'AccessKeyId',
        'accessKeySecret' => 'AccessKeySecret',
        'expiration'      => 'Expiration',
        'securityToken'   => 'SecurityToken',
    ];

    public function validate()
    {
    }

    public function toMap()
    {
        $res = [];
        if (null !== $this->accessKeyId) {
            $res['AccessKeyId'] = $this->accessKeyId;
        }
        if (null !== $this->accessKeySecret) {
            $res['AccessKeySecret'] = $this->accessKeySecret;
        }
        if (null !== $this->expiration) {
            $res['Expiration'] = $this->expiration;
        }
        if (null !== $this->securityToken) {
            $res['SecurityToken'] = $this->securityToken;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return credentials
     */
    public static function fromMap($map = [])
    {
        $model = new self();
        if (isset($map['AccessKeyId'])) {
            $model->accessKeyId = $map['AccessKeyId'];
        }
        if (isset($map['AccessKeySecret'])) {
            $model->accessKeySecret = $map['AccessKeySecret'];
        }
        if (isset($map['Expiration'])) {
            $model->expiration = $map['Expiration'];
        }
        if (isset($map['SecurityToken'])) {
            $model->securityToken = $map['SecurityToken'];
        }

        return $model;
    }
}
