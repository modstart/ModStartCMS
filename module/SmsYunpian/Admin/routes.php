<?php



$router->match(['get', 'post'], 'sms_' . \Module\SmsYunpian\Provider\SmsSender::NAME . '/config/setting', 'ConfigController@setting');



