<?php

namespace App\Filament\Resources\Administration\Settings\Pages;

use App\Filament\Resources\Administration\Settings\SettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;
}
