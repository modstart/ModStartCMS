<?php



$router->match(['get', 'post'], 'sms_' . \Module\SmsAliyun\Provider\SmsSender::NAME . '/config/setting', 'ConfigController@setting');



