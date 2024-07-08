<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithSAMLResponseBody;

use AlibabaCloud\Tea\Model;

class credentials extends Model
{
    /**
     * @description The AccessKey ID.
     *
     * @example STS.L4aBSCSJVMuKg5U1****
     *
     * @var string
     */
    public $accessKeyId;

    /**
     * @description The AccessKey secret.
     *
     * @example wyLTSmsyPGP1ohvvw8xYgB29dlGI8KMiH2pK****
     *
     * @var string
     */
    public $accessKeySecret;

    /**
     * @description The time when the STS token expires. The time is displayed in UTC.
     *
     * @example 2015-04-09T11:52:19Z
     *
     * @var string
     */
    public $expiration;

    /**
     * @description The STS token.
     *
     * > Alibaba Cloud STS does not impose limits on the length of STS tokens. We strongly recommend that you do not specify a maximum length for STS tokens.
     * @example ********
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
