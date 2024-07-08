<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models;

use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithSAMLResponseBody\assumedRoleUser;
use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithSAMLResponseBody\credentials;
use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithSAMLResponseBody\SAMLAssertionInfo;
use AlibabaCloud\Tea\Model;

class AssumeRoleWithSAMLResponseBody extends Model
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

    /**
     * @description The information in the SAML assertion.
     *
     * @var SAMLAssertionInfo
     */
    public $SAMLAssertionInfo;
    protected $_name = [
        'assumedRoleUser'   => 'AssumedRoleUser',
        'credentials'       => 'Credentials',
        'requestId'         => 'RequestId',
        'SAMLAssertionInfo' => 'SAMLAssertionInfo',
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
        if (null !== $this->SAMLAssertionInfo) {
            $res['SAMLAssertionInfo'] = null !== $this->SAMLAssertionInfo ? $this->SAMLAssertionInfo->toMap() : null;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return AssumeRoleWithSAMLResponseBody
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
        if (isset($map['SAMLAssertionInfo'])) {
            $model->SAMLAssertionInfo = SAMLAssertionInfo::fromMap($map['SAMLAssertionInfo']);
        }

        return $model;
    }
}
