<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models;

use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithOIDCResponseBody\assumedRoleUser;
use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithOIDCResponseBody\credentials;
use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithOIDCResponseBody\OIDCTokenInfo;
use AlibabaCloud\Tea\Model;

class AssumeRoleWithOIDCResponseBody extends Model
{
    /**
     * @description The temporary identity that you use to assume the RAM role.
     *
     * @var assumedRoleUser
     */
    public $assumedRoleUser;

    /**
     * @description The access credentials.
     *
     * @var credentials
     */
    public $credentials;

    /**
     * @description The information about the OIDC token.
     *
     * @var OIDCTokenInfo
     */
    public $OIDCTokenInfo;

    /**
     * @description The ID of the request.
     *
     * @example 3D57EAD2-8723-1F26-B69C-F8707D8B565D
     *
     * @var string
     */
    public $requestId;
    protected $_name = [
        'assumedRoleUser' => 'AssumedRoleUser',
        'credentials'     => 'Credentials',
        'OIDCTokenInfo'   => 'OIDCTokenInfo',
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
        if (null !== $this->OIDCTokenInfo) {
            $res['OIDCTokenInfo'] = null !== $this->OIDCTokenInfo ? $this->OIDCTokenInfo->toMap() : null;
        }
        if (null !== $this->requestId) {
            $res['RequestId'] = $this->requestId;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return AssumeRoleWithOIDCResponseBody
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
        if (isset($map['OIDCTokenInfo'])) {
            $model->OIDCTokenInfo = OIDCTokenInfo::fromMap($map['OIDCTokenInfo']);
        }
        if (isset($map['RequestId'])) {
            $model->requestId = $map['RequestId'];
        }

        return $model;
    }
}
