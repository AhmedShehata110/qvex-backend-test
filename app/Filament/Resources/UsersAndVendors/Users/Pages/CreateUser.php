<?php

namespace App\Filament\Resources\UsersAndVendors\Users\Pages;

use App\Filament\Resources\UsersAndVendors\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
