<?php

namespace Qcloud\Cos;

function region_map($region)
{
    $regionmap = array(
        'cn-east' => 'ap-shanghai',
        'cn-south' => 'ap-guangzhou',
        'cn-north' => 'ap-beijing-1',
        'cn-south-2' => 'ap-guangzhou-2',
        'cn-southwest' => 'ap-chengdu',
        'sg' => 'ap-singapore',
        'tj' => 'ap-beijing-1',
        'bj' => 'ap-beijing',
        'sh' => 'ap-shanghai',
        'gz' => 'ap-guangzhou',
        'cd' => 'ap-chengdu',
        'sgp' => 'ap-singapore'
    );
    if (isset($regionmap[$region])) {
        return $regionmap[$region];
    }
    return $region;
}

function processCosConfig($config)
{
    $config['region'] = !empty($config['region']) ? region_map($config['region']) : $config['region'];
    $config['secretId'] = trim($config['credentials']['secretId']);
    $config['secretKey'] = trim($config['credentials']['secretKey']);
    $config['token'] = !empty($config['credentials']['token']) ? trim($config['credentials']['token']) : $config['credentials']['token'];
    $config['appId'] = $config['credentials']['appId'];
    $config['anonymous'] = $config['credentials']['anonymous'];
    unset($config['credentials']);

    if (isset($config['schema'])) {
        $config['scheme'] = $config['schema'];
        unset($config['schema']);
    }

    if (isset($config['locationWithSchema'])) {
        $config['locationWithScheme'] = $config['locationWithSchema'];
        unset($config['locationWithSchema']);
    }

    return $config;
}

function encodeKey($key)
{
    return str_replace('%2F', '/', rawurlencode($key));
}

function endWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

function startWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, 0, $length) === $needle);
}

function headersMap($command, $request)
{
    $headermap = array(
        'TransferEncoding' => 'Transfer-Encoding',
        'ChannelId' => 'x-cos-channel-id'
    );
    foreach ($headermap as $key => $value) {
        if (isset($command[$key])) {
            $request = $request->withHeader($value, $command[$key]);
        }
    }
    return $request;
}

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }
}
