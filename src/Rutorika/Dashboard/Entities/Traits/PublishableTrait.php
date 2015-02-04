<?php

namespace Rutorika\Dashboard\Entities\Traits;

/**
 * Class PublishableTrait
 *
 * @package Rutorika\Dashboard\Entities\Traits
 */
trait PublishableTrait
{
    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return mixed
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}