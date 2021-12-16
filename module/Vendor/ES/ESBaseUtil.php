<?php

namespace Module\Vendor\ES;

use Elasticsearch\ClientBuilder;

class ESBaseUtil
{
    public static function createIndex($table, $version = 'v1')
    {
        if (!isset(static::$tableProperties[$table])) {
            throw new \Exception('不存在的索引');
        }
        if (!self::client()->indices()->exists(['index' => $table . '_' . $version])) {
            /**
             * static::client()->indices()->putSettings([
             * 'index' => 'customer_v1',
             * 'body' => [
             * 'max_result_window' => 50000,
             * ]
             * ])
             */
            self::client()->indices()->create([
                'index' => $table . '_' . $version,
                'body' => [
                    'settings' => [
                        'max_result_window' => 50000,
                    ],
                    'mappings' => [
                        $table => [
                            'properties' => static::$tableProperties[$table],
                        ]
                    ]
                ]
            ]);
        }
    }

    public static function deleteIndex($table, $version = 'v1')
    {
        if (!isset(static::$tableProperties[$table])) {
            throw new \Exception('不存在的索引');
        }
        if (self::client()->indices()->exists(['index' => $table . '_' . $version])) {
            self::client()->indices()->delete(['index' => $table . '_' . $version]);
        }
    }

    public static function deleteAlias($table, $version = 'v1')
    {
        if (!isset(static::$tableProperties[$table])) {
            throw new \Exception('不存在的索引');
        }
        if (self::client()->indices()->existsAlias(['name' => $table])) {
            self::client()->indices()->deleteAlias([
                'index' => $table . '_' . $version,
                'name' => $table,
            ]);
        }
    }

    public static function putAlias($table, $version = 'v1')
    {
        if (!isset(static::$tableProperties[$table])) {
            throw new \Exception('不存在的索引');
        }
        if (!self::client()->indices()->existsAlias(['name' => $table])) {
            self::client()->indices()->putAlias([
                'index' => $table . '_' . $version,
                'name' => $table,
            ]);
        }
    }

    public static function stats()
    {
        return self::client()->indices()->stats();
    }

    public static function statsIndex($index)
    {
        $stats = self::client()->indices()->stats();
        if (!empty($stats['indices'])) {
            foreach ($stats['indices'] as $k => $v) {
                if (preg_match("/^${index}_v\\d+$/", $k)) {
                    return $v;
                }
            }
        }
        return null;
    }

    /**
     * @return \Elasticsearch\Client
     */
    public static function client($esConfig = [])
    {
        static $client = null;
        if (null === $client) {
            $config = modstart_config();
            $hosts = [
                array_merge([
                    'host' => $config->getWithEnv('moduleESHost'),
                    'port' => $config->getWithEnv('moduleESPort'),
                    'scheme' => 'http',
                    'user' => $config->getWithEnv('moduleESUser'),
                    'pass' => $config->getWithEnv('moduleESPass')
                ], $esConfig),
            ];
            $client = ClientBuilder::create()->setHosts($hosts)->build();
        }
        return $client;
    }

    public static function save($table, $record)
    {
        if (!isset(static::$tableProperties[$table])) {
            throw new \Exception('不存在的索引');
        }
        $client = static::client();
        $response = self::client()->index([
            'index' => $table,
            'type' => $table,
            'id' => $record['id'],
            'body' => $record,
        ]);
    }

    public static function saveBulk($table, $records)
    {
        if (!isset(static::$tableProperties[$table])) {
            throw new \Exception('不存在的索引');
        }
        $body = [];
        foreach ($records as $record) {
            $body[] = [
                'index' => [
                    '_index' => $table,
                    '_id' => $record['id'],
                    '_type' => $table,
                ],
            ];
            $body[] = $record;
        }
        $client = static::client();
        $response = self::client()->bulk([
            'index' => $table,
            'type' => $table,
            'body' => $body,
        ]);
    }
}