<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models;

use AlibabaCloud\Tea\Model;

class AssumeRoleWithSAMLRequest extends Model
{
    /**
     * @description The validity period of the STS token. Unit: seconds.
     *
     * You can call the CreateRole or UpdateRole operation to configure the `MaxSessionDuration` parameter. For more information, see [CreateRole](~~28710~~) or [UpdateRole](~~28712~~).
     * @example 3600
     *
     * @var int
     */
    public $durationSeconds;

    /**
     * @description The policy that specifies the permissions of the returned STS token. You can use this parameter to grant the STS token fewer permissions than the permissions granted to the RAM role.
     *
     *   If you specify this parameter, the permissions of the returned STS token are the permissions that are included in the value of this parameter and owned by the RAM role.
     *   If you do not specify this parameter, the returned STS token has all the permissions of the RAM role.
     *
     * The value must be 1 to 2,048 characters in length.
     * @example url_encoded_policy
     *
     * @var string
     */
    public $policy;

    /**
     * @description The ARN of the RAM role.
     *
     * You can view the ARN in the RAM console or by calling operations.
     *
     *   For more information about how to view the ARN in the RAM console, see [How do I view the ARN of the RAM role?](~~39744~~).
     *   For more information about how to view the ARN by calling operations, see [ListRoles](~~28713~~) or [GetRole](~~28711~~).
     *
     * @example acs:ram::123456789012****:role/adminrole
     *
     * @var string
     */
    public $roleArn;

    /**
     * @description The Base64-encoded SAML assertion.
     *
     * > A complete SAML response rather than a single SAMLAssertion field must be retrieved from the external IdP.
     * @example base64_encoded_saml_assertion
     *
     * @var string
     */
    public $SAMLAssertion;

    /**
     * @description The Alibaba Cloud Resource Name (ARN) of the SAML IdP that is created in the RAM console.
     *
     * You can view the ARN in the RAM console or by calling operations.
     *
     *   For more information about how to view the ARN in the RAM console, see [How do I view the ARN of a RAM role?](~~116795~~)
     *   For more information about how to view the ARN by calling operations, see [GetSAMLProvider](~~186833~~) or [ListSAMLProviders](~~186851~~).
     *
     * @example acs:ram::123456789012****:saml-provider/company1
     *
     * @var string
     */
    public $SAMLProviderArn;
    protected $_name = [
        'durationSeconds' => 'DurationSeconds',
        'policy'          => 'Policy',
        'roleArn'         => 'RoleArn',
        'SAMLAssertion'   => 'SAMLAssertion',
        'SAMLProviderArn' => 'SAMLProviderArn',
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
        if (null !== $this->policy) {
            $res['Policy'] = $this->policy;
        }
        if (null !== $this->roleArn) {
            $res['RoleArn'] = $this->roleArn;
        }
        if (null !== $this->SAMLAssertion) {
            $res['SAMLAssertion'] = $this->SAMLAssertion;
        }
        if (null !== $this->SAMLProviderArn) {
            $res['SAMLProviderArn'] = $this->SAMLProviderArn;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return AssumeRoleWithSAMLRequest
     */
    public static function fromMap($map = [])
    {
        $model = new self();
        if (isset($map['DurationSeconds'])) {
            $model->durationSeconds = $map['DurationSeconds'];
        }
        if (isset($map['Policy'])) {
            $model->policy = $map['Policy'];
        }
        if (isset($map['RoleArn'])) {
            $model->roleArn = $map['RoleArn'];
        }
        if (isset($map['SAMLAssertion'])) {
            $model->SAMLAssertion = $map['SAMLAssertion'];
        }
        if (isset($map['SAMLProviderArn'])) {
            $model->SAMLProviderArn = $map['SAMLProviderArn'];
        }

        return $model;
    }
}
