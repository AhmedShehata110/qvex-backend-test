<?php

namespace App\Filament\Resources\Locations\Cities\Pages;

use App\Filament\Resources\Locations\Cities\CitiesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCities extends CreateRecord
{
    protected static string $resource = CitiesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure translatable fields are properly formatted
        if (isset($data['name'])) {
            $data['name'] = [
                'en' => $data['name']['en'] ?? '',
                'ar' => $data['name']['ar'] ?? '',
            ];
        }

        return $data;
    }
}
