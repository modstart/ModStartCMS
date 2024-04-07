<?php

namespace ModStart\Grid\Filter;

use ModStart\Core\Dao\ModelUtil;

/**
 * 多条件搜索
 *
 * Class Likes
 * @package ModStart\Grid\Filter
 */
class Likes extends AbstractFilter
{
    /**
     * Get condition of this filter.
     *
     * @param array $search
     *
     * @return array|mixed|void
     */
    public function condition($searchInfo)
    {
        if (isset($searchInfo['likes']) && is_array($searchInfo['likes'])) {
            $conditions = [];
            foreach ($searchInfo['likes'] as $like) {
                if (empty($like)) {
                    continue;
                }
                $conditions[] = $this->buildCondition($this->column, 'like', "%" . ModelUtil::quoteLikeKeywords($like) . "%");
            }
            if (!empty($conditions)) {
                return $conditions;
            }
        }
        return null;
    }

    public function groupTags($groupTags, $serializeType = null)
    {
        $this->field = new Field\GroupTags($this);
        $this->field->options($groupTags);
        $this->field->serializeType($serializeType);
        return $this;
    }
}
