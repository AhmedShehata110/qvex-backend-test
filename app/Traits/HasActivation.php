<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasActivation
{
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function isInActive(): bool
    {
        return $this->is_active === false;
    }

    public function inActive(?Model $model = null): void
    {
        if (isset($model)) {
            $model->update(['is_active' => false]);
        } else {
            $this->update(['is_active' => false]);
        }
    }

    public function active(?Model $model = null): void
    {
        if (isset($model)) {
            $model->update(['is_active' => true]);
        } else {
            $this->update(['is_active' => true]);
        }
    }

    public function toggleActivation(?Model $model = null): void
    {
        if (isset($model)) {
            $model->update(['is_active' => ! $model->is_active]);
        } else {
            $this->update(['is_active' => ! $this->is_active]);
        }
    }

    public function toggleStatus(?Model $model = null): void
    {
        if (isset($model)) {
            $model->update(['show' => ! $model->show]);
        } else {
            $this->update(['show' => ! $this->show]);
        }
    }

    /*
     * Scopes
     * */
    public function scopeWhereIsActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWhereIsInActive($query)
    {
        return $query->where('is_active', false);
    }
}
