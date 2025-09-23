<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

trait TranslatableUniqueValidation
{
    public function generateTranslatableRules(string $translationTable, string $foreignKey, ?int $ignoreId = null, ?string $mainTable = 'static_pages'): array
    {
        $rules = [];

        foreach (config('translatable.locales') as $locale) {
            $uniqueRule = Rule::unique($translationTable, 'name')
                ->where(function ($query) use ($locale, $translationTable, $foreignKey, $mainTable) {
                    return $query->where('locale', $locale)
                        ->whereExists(function ($subQuery) use ($translationTable, $foreignKey, $mainTable) {
                            $subQuery->select(DB::raw(1))
                                ->from($mainTable)
                                ->whereColumn($mainTable.'.id', "$translationTable.$foreignKey")
                                ->whereNull($mainTable.'.deleted_at'); // SoftDeletes
                        });
                });

            if ($ignoreId) {
                $uniqueRule->ignore($ignoreId, $foreignKey);
            }

            $rules["$locale.name"] = ['nullable', 'string', 'max:255', $uniqueRule];
            $rules["$locale.description"] = ['nullable', 'string'];
        }

        return $rules;
    }
}
