<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models;

use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleResponseBody\assumedRoleUser;
use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleResponseBody\credentials;
use AlibabaCloud\Tea\Model;

class AssumeRoleResponseBody extends Model
{
    /**
     * @description The temporary identity that you use to assume the RAM role.
     *
     * @var assumedRoleUser
     */
    public $assumedRoleUser;

    /**
     * @description The STS credentials.
     *
     * @var credentials
     */
    public $credentials;

    /**
     * @description The ID of the request.
     *
     * @example 6894B13B-6D71-4EF5-88FA-F32781734A7F
     *
     * @var string
     */
    public $requestId;
    protected $_name = [
        'assumedRoleUser' => 'AssumedRoleUser',
        'credentials'     => 'Credentials',
        'requestId'       => 'RequestId',
    ];

    public function validate()
    {
    }

    public function toMap()
    {
        $res = [];
        if (null !== $this->assumedRoleUser) {
            $res['AssumedRoleUser'] = null !== $this->assumedRoleUser ? $this->assumedRoleUser->toMap() : null;
        }
        if (null !== $this->credentials) {
            $res['Credentials'] = null !== $this->credentials ? $this->credentials->toMap() : null;
        }
        if (null !== $this->requestId) {
            $res['RequestId'] = $this->requestId;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return AssumeRoleResponseBody
     */
    public static function fromMap($map = [])
    {
        $model = new self();
        if (isset($map['AssumedRoleUser'])) {
            $model->assumedRoleUser = assumedRoleUser::fromMap($map['AssumedRoleUser']);
        }
        if (isset($map['Credentials'])) {
            $model->credentials = credentials::fromMap($map['Credentials']);
        }
        if (isset($map['RequestId'])) {
            $model->requestId = $map['RequestId'];
        }

        return $model;
    }
}
