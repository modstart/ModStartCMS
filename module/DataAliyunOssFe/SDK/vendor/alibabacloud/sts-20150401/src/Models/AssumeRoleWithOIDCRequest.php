<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models;

use AlibabaCloud\Tea\Model;

class AssumeRoleWithOIDCRequest extends Model
{
    /**
     * @description The validity period of the STS token. Unit: seconds.
     *
     * For more information about how to specify `MaxSessionDuration`, see [CreateRole](~~28710~~) or [UpdateRole](~~28712~~).
     * @example 3600
     *
     * @var int
     */
    public $durationSeconds;

    /**
     * @description The Alibaba Cloud Resource Name (ARN) of the OIDC IdP.
     *
     * You can view the ARN in the RAM console or by calling operations.
     *
     *   For more information about how to view the ARN in the RAM console, see [View the information about an OIDC IdP](~~327123~~).
     *   For more information about how to view the ARN by calling operations, see [GetOIDCProvider](~~327126~~) or [ListOIDCProviders](~~327127~~).
     *
     * @example acs:ram::113511544585****:oidc-provider/TestOidcIdp
     *
     * @var string
     */
    public $OIDCProviderArn;

    /**
     * @description The OIDC token that is issued by the external IdP.
     *
     * > You must enter the original OIDC token. You do not need to enter the Base64-encoded OIDC token.
     * @example eyJraWQiOiJKQzl3eHpyaHFKMGd0****
     *
     * @var string
     */
    public $OIDCToken;

    /**
     * @description The policy that specifies the permissions of the returned STS token. You can use this parameter to grant the STS token fewer permissions than the permissions granted to the RAM role.
     *
     *   If you specify this parameter, the permissions of the returned STS token are the permissions that are included in the value of this parameter and owned by the RAM role.
     *   If you do not specify this parameter, the returned STS token has all the permissions of the RAM role.
     *
     * The value must be 1 to 2,048 characters in length.
     * @example {"Statement": [{"Action": ["*"],"Effect": "Allow","Resource": ["*"]}],"Version":"1"}
     *
     * @var string
     */
    public $policy;

    /**
     * @description The ARN of the RAM role.
     *
     * You can view the ARN in the RAM console or by calling operations.
     *
     *   For more information about how to view the ARN in the RAM console, see [How do I view the ARN of the RAM role?](~~39744~~)
     *   For more information about how to view the ARN by calling operations, see [ListRoles](~~28713~~) or [GetRole](~~28711~~).
     *
     * @example acs:ram::113511544585****:role/testoidc
     *
     * @var string
     */
    public $roleArn;

    /**
     * @description The custom name of the role session.
     *
     * The value must be 2 to 64 characters in length.
     * @example TestOidcAssumedRoleSession
     *
     * @var string
     */
    public $roleSessionName;
    protected $_name = [
        'durationSeconds' => 'DurationSeconds',
        'OIDCProviderArn' => 'OIDCProviderArn',
        'OIDCToken'       => 'OIDCToken',
        'policy'          => 'Policy',
        'roleArn'         => 'RoleArn',
        'roleSessionName' => 'RoleSessionName',
    ];

    public function validate()
    {
    }

    public function toMap()
    {
        $res = [];
        if (null !== $this->durationSeconds) {
            $res['DurationSeconds'] = $this->durationSeconds;
        }
        if (null !== $this->OIDCProviderArn) {
            $res['OIDCProviderArn'] = $this->OIDCProviderArn;
        }
        if (null !== $this->OIDCToken) {
            $res['OIDCToken'] = $this->OIDCToken;
        }
        if (null !== $this->policy) {
            $res['Policy'] = $this->policy;
        }
        if (null !== $this->roleArn) {
            $res['RoleArn'] = $this->roleArn;
        }
        if (null !== $this->roleSessionName) {
            $res['RoleSessionName'] = $this->roleSessionName;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return AssumeRoleWithOIDCRequest
     */
    public static function fromMap($map = [])
    {
        $model = new self();
        if (isset($map['DurationSeconds'])) {
            $model->durationSeconds = $map['DurationSeconds'];
        }
        if (isset($map['OIDCProviderArn'])) {
            $model->OIDCProviderArn = $map['OIDCProviderArn'];
        }
        if (isset($map['OIDCToken'])) {
            $model->OIDCToken = $map['OIDCToken'];
        }
        if (isset($map['Policy'])) {
            $model->policy = $map['Policy'];
        }
        if (isset($map['RoleArn'])) {
            $model->roleArn = $map['RoleArn'];
        }
        if (isset($map['RoleSessionName'])) {
            $model->roleSessionName = $map['RoleSessionName'];
        }

        return $model;
    }
}
