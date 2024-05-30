<?php

namespace ModStart\Core\Util;

class CAUtil
{
    public static function getCAInfo($url)
    {
        try {
            $urlInfo = parse_url($url);
            if (empty($urlInfo['port'])) {
                $urlInfo['port'] = 443;
            }
            $get = stream_context_create(array("ssl" => array(
                "capture_peer_cert" => TRUE,
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )));
            $fp = stream_socket_client("ssl://" . $urlInfo['host'] . ":" . $urlInfo['port'], $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $get);
            $cert = stream_context_get_params($fp);
            @stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
            @fclose($fp);
            $cert = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
            $info = [];
            $info['timeFrom'] = date('Y-m-d H:i:s', $cert['validFrom_time_t']);
            $info['timeTo'] = date('Y-m-d H:i:s', $cert['validTo_time_t']);
            $info['domains'] = [];
            if (!empty($cert['extensions']['subjectAltName'])) {
                foreach (explode(',', $cert['extensions']['subjectAltName']) as $one) {
                    list($_, $domain) = explode(':', $one);
                    $info['domains'][] = trim($domain);
                }
            }
            return $info;
        } catch (\Exception $e) {
            @stream_socket_shutdown($fp, STREAM_SHUT_RDWR);
            @fclose($fp);
            return null;
        }
    }
}
