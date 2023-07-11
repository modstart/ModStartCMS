<?php

namespace ModStart\Grid\Filter;

use ModStart\Core\Util\StrUtil;

class Like extends AbstractFilter
{
    private $handle = null;

    /**
     * Get condition of this filter.
     *
     * @param array $search
     *
     * @return array|mixed|void
     */
    public function condition($searchInfo)
    {
        if (isset($searchInfo['like']) && $searchInfo['like'] !== '') {
            if (!empty($this->handle)) {
                return call_user_func_array($this->handle, [
                    $searchInfo['like']
                ]);
            } else {
                return $this->buildCondition($this->column, 'like', "%${searchInfo['like']}%");
            }
        }
        return null;
    }

    public function handle(\Closure $closure)
    {
        $this->handle = $closure;
    }

    public function wordSplit()
    {
        $this->handle = function ($keywords) {
            return [
                [
                    'where' => [
                        function ($query) use ($keywords) {
                            $pcs = StrUtil::wordSplit($keywords);
                            foreach ($pcs as $p) {
                                $query->where($this->column, 'like', '%' . $p . '%');
                            }
                        }
                    ]
                ],
            ];
        };
    }
}
