<?php

namespace ModStart\Core\Util;


/**
 * @Util Socket 工具
 */
class SocketUtil
{
    /**
     * @Util 判断指定 IP 和端口是否可达（TCP 连接测试）
     * @param $ip string IP 地址
     * @param $port int 端口
     * @param $timeout int 超时时间（秒），默认 3
     * @return bool
     */
    public static function isTCPConnectable($ip, $port, $timeout = 3)
    {
        try {
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            $connect_timeval = array("sec" => $timeout, "usec" => 0);
            socket_set_option(
                $socket,
                SOL_SOCKET,
                SO_SNDTIMEO,
                $connect_timeval
            );
            socket_set_option(
                $socket,
                SOL_SOCKET,
                SO_RCVTIMEO,
                $connect_timeval
            );
            if (socket_connect($socket, $ip, $port)) {
                @socket_close($socket);
                return true;
            }
        } catch (\Exception $e) {
            @socket_close($socket);
        }
        return false;
    }
}
