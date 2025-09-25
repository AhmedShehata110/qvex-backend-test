<?php

namespace App\Filament\Resources\Locations\Countries\Pages;

use App\Filament\Resources\Locations\Countries\CountriesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCountries extends EditRecord
{
    protected static string $resource = CountriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Handle translatable fields for editing
        if (isset($data['name']) && is_array($data['name'])) {
            $data['name.en'] = $data['name']['en'] ?? '';
            $data['name.ar'] = $data['name']['ar'] ?? '';
            unset($data['name']);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure translatable fields are properly formatted for saving
        if (isset($data['name.en']) || isset($data['name.ar'])) {
            $data['name'] = [
                'en' => $data['name.en'] ?? '',
                'ar' => $data['name.ar'] ?? '',
            ];
            unset($data['name.en'], $data['name.ar']);
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
