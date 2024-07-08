<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithOIDCResponseBody;

use AlibabaCloud\Tea\Model;

class OIDCTokenInfo extends Model
{
    /**
     * @description The audience. If multiple audiences are returned, the audiences are separated by commas (,).
     *
     * The audience is represented by the `aud` field in the OIDC Token.
     * @example 496271242565057****
     *
     * @var string
     */
    public $clientIds;

    /**
     * @var string
     */
    public $expirationTime;

    /**
     * @var string
     */
    public $issuanceTime;

    /**
     * @description The URL of the issuer,
     *
     * which is represented by the `iss` field in the OIDC Token.
     * @example https://dev-xxxxxx.okta.com
     *
     * @var string
     */
    public $issuer;

    /**
     * @description The subject,
     *
     * which is represented by the `sub` field in the OIDC Token.
     * @example KryrkIdjylZb7agUgCEf****
     *
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $verificationInfo;
    protected $_name = [
        'clientIds'        => 'ClientIds',
        'expirationTime'   => 'ExpirationTime',
        'issuanceTime'     => 'IssuanceTime',
        'issuer'           => 'Issuer',
        'subject'          => 'Subject',
        'verificationInfo' => 'VerificationInfo',
    ];

    public function validate()
    {
    }

    public function toMap()
    {
        $res = [];
        if (null !== $this->clientIds) {
            $res['ClientIds'] = $this->clientIds;
        }
        if (null !== $this->expirationTime) {
            $res['ExpirationTime'] = $this->expirationTime;
        }
        if (null !== $this->issuanceTime) {
            $res['IssuanceTime'] = $this->issuanceTime;
        }
        if (null !== $this->issuer) {
            $res['Issuer'] = $this->issuer;
        }
        if (null !== $this->subject) {
            $res['Subject'] = $this->subject;
        }
        if (null !== $this->verificationInfo) {
            $res['VerificationInfo'] = $this->verificationInfo;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return OIDCTokenInfo
     */
    public static function fromMap($map = [])
    {
        $model = new self();
        if (isset($map['ClientIds'])) {
            $model->clientIds = $map['ClientIds'];
        }
        if (isset($map['ExpirationTime'])) {
            $model->expirationTime = $map['ExpirationTime'];
        }
        if (isset($map['IssuanceTime'])) {
            $model->issuanceTime = $map['IssuanceTime'];
        }
        if (isset($map['Issuer'])) {
            $model->issuer = $map['Issuer'];
        }
        if (isset($map['Subject'])) {
            $model->subject = $map['Subject'];
        }
        if (isset($map['VerificationInfo'])) {
            $model->verificationInfo = $map['VerificationInfo'];
        }

        return $model;
    }
}
