<?php

namespace App\Http\Filters;

use Illuminate\Support\Facades\App;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|$this filter(BaseFilters $filters = null)
 */
trait Filterable
{
    public function scopeFilter($query, ?BaseFilters $filters = null)
    {
        if (! $filters) {
            $filters = App::make($this->filter);
        }

        return $filters->apply($query);
    }

    /**
     * Get the number of models to return per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return request('perPage', parent::getPerPage());
    }
}
