<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithOIDCResponseBody;

use AlibabaCloud\Tea\Model;

class assumedRoleUser extends Model
{
    /**
     * @description The ARN of the temporary identity that you use to assume the RAM role.
     *
     * @example acs:ram::113511544585****:role/testoidc/TestOidcAssumedRoleSession
     *
     * @var string
     */
    public $arn;

    /**
     * @description The ID of the temporary identity that you use to assume the RAM role.
     *
     * @example 33157794895460****
     *
     * @var string
     */
    public $assumedRoleId;
    protected $_name = [
        'arn'           => 'Arn',
        'assumedRoleId' => 'AssumedRoleId',
    ];

    public function validate()
    {
    }

    public function toMap()
    {
        $res = [];
        if (null !== $this->arn) {
            $res['Arn'] = $this->arn;
        }
        if (null !== $this->assumedRoleId) {
            $res['AssumedRoleId'] = $this->assumedRoleId;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return assumedRoleUser
     */
    public static function fromMap($map = [])
    {
        $model = new self();
        if (isset($map['Arn'])) {
            $model->arn = $map['Arn'];
        }
        if (isset($map['AssumedRoleId'])) {
            $model->assumedRoleId = $map['AssumedRoleId'];
        }

        return $model;
    }
}
