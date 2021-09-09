<?php

namespace Module\Vendor\Support;


class ResponseCodes
{
    const API_TOKEN_EMPTY = 1000;
    const LOGIN_REQUIRED = 1001;
    const CAPTCHA_ERROR = 1002;
    const PERMIT_DENIED = 1003;

    const DEFAULT_ERROR = -1;
}