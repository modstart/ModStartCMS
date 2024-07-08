<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithSAMLResponseBody;

use AlibabaCloud\Tea\Model;

class assumedRoleUser extends Model
{
    /**
     * @description The ARN of the temporary identity that you use to assume the RAM role.
     *
     * @example acs:sts::123456789012****:assumed-role/AdminRole/alice
     *
     * @var string
     */
    public $arn;

    /**
     * @description The ID of the temporary identity that you use to assume the RAM role.
     *
     * @example 34458433936495****:alice
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
