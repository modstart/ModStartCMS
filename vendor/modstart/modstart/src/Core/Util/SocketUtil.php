<?php

namespace ModStart\Core\Util;


class SocketUtil
{
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
