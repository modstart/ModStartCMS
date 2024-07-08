<?php

// This file is auto-generated, don't edit it. Thanks.

namespace AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleWithSAMLResponseBody;

use AlibabaCloud\Tea\Model;

class SAMLAssertionInfo extends Model
{
    /**
     * @description The value in the `Issuer` element in the SAML assertion.
     *
     * @example http://example.com/adfs/services/trust
     *
     * @var string
     */
    public $issuer;

    /**
     * @description The `Recipient` attribute of the SubjectConfirmationData sub-element. SubjectConfirmationData is a sub-element of the `Subject` element in the SAML assertion.
     *
     * @example https://signin.aliyun.com/saml-role/SSO
     *
     * @var string
     */
    public $recipient;

    /**
     * @description The value in the NameID sub-element of the `Subject` element in the SAML assertion.
     *
     * @example alice@example.com
     *
     * @var string
     */
    public $subject;

    /**
     * @description The Format attribute of the `NameID` element in the SAML assertion. If the Format attribute is prefixed with `urn:oasis:names:tc:SAML:2.0:nameid-format:`, the prefix is not included in the value of this parameter. For example, if the value of the Format attribute is urn:oasis:names:tc:SAML:2.0:nameid-format:persistent/transient, the value of this parameter is `persistent/transient`.
     *
     * @example persistent
     *
     * @var string
     */
    public $subjectType;
    protected $_name = [
        'issuer'      => 'Issuer',
        'recipient'   => 'Recipient',
        'subject'     => 'Subject',
        'subjectType' => 'SubjectType',
    ];

    public function validate()
    {
    }

    public function toMap()
    {
        $res = [];
        if (null !== $this->issuer) {
            $res['Issuer'] = $this->issuer;
        }
        if (null !== $this->recipient) {
            $res['Recipient'] = $this->recipient;
        }
        if (null !== $this->subject) {
            $res['Subject'] = $this->subject;
        }
        if (null !== $this->subjectType) {
            $res['SubjectType'] = $this->subjectType;
        }

        return $res;
    }

    /**
     * @param array $map
     *
     * @return SAMLAssertionInfo
     */
    public static function fromMap($map = [])
    {
        $model = new self();
        if (isset($map['Issuer'])) {
            $model->issuer = $map['Issuer'];
        }
        if (isset($map['Recipient'])) {
            $model->recipient = $map['Recipient'];
        }
        if (isset($map['Subject'])) {
            $model->subject = $map['Subject'];
        }
        if (isset($map['SubjectType'])) {
            $model->subjectType = $map['SubjectType'];
        }

        return $model;
    }
}
