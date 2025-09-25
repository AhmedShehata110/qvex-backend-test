<?php

namespace App\Filament\Resources\Content\BlogPosts\Pages;

use App\Filament\Resources\Content\BlogPosts\BlogPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;
}
