<?php

namespace App\Filament\Resources\SalesAndTransactions\Payments\Pages;

use App\Filament\Resources\SalesAndTransactions\Payments\PaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
