<?php


namespace ModStart\Core\Input;

/**
 * Class StaticQueryUtil
 * @package ModStart\Core\Input
 *
 * @example
 *
 * $a = StaticQueryUtil::build(['name' => 'n', 'age' => 'a']);
 * $query = $a->create(['name' => 1, 'age' => 2]);
 * var_dump($query);
 * $query = $a->parse($query);
 * var_dump($query);
 */
class StaticQueryUrl
{
    public $map = [];
    public $glue = '-';
    private $query = [];

    public function __construct($map = [])
    {
        $this->map = $map;
    }

    public static function build($map = ['key' => 'k'])
    {
        return new static($map);
    }

    public function add($key, $k)
    {
        $this->map[$key] = $k;
    }

    public function create($query)
    {
        $url = [];
        foreach ($query as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (isset($this->map[$k])) {
                if (is_array($v)) {
                    $url[] = $this->map[$k] . $v[0];
                } else {
                    $url[] = $this->map[$k] . urlencode("$v");
                }
            }
        }
        sort($url);
        return join($this->glue, $url);
    }

    public function createMerge($query)
    {
        return $this->create(array_merge($this->query, $query));
    }

    public function parse($queryString)
    {
        $query = [];
        $part = explode($this->glue, $queryString);
        foreach ($part as $item) {
            foreach ($this->map as $key => $k) {
                if (strpos($item, $k) === 0) {
                    $query[$key] = substr($item, strlen($k));
                }
            }
        }
        $this->query = $query;
        return $query;
    }

    public function parseMergeQuery($query)
    {
        foreach ($query as $k => $v) {
            $this->query[$k] = $v;
        }
    }

    public function getQuery()
    {
        return $this->query;
    }

}
