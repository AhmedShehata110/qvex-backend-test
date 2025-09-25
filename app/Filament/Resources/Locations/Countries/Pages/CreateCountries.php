<?php

namespace App\Filament\Resources\Locations\Countries\Pages;

use App\Filament\Resources\Locations\Countries\CountriesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCountries extends CreateRecord
{
    protected static string $resource = CountriesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure translatable fields are properly formatted
        if (isset($data['name'])) {
            $data['name'] = [
                'en' => $data['name']['en'] ?? '',
                'ar' => $data['name']['ar'] ?? '',
            ];
        }

        // Convert codes to uppercase
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        if (isset($data['currency_code'])) {
            $data['currency_code'] = strtoupper($data['currency_code']);
        }

        return $data;
    }
}
