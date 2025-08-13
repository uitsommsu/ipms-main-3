<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PatentSubmissionMessage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static bool $shouldRegisterNavigation = false;

    protected  ?string $heading = 'Patent Submission Confirmation';

    protected static string $view = 'filament.pages.patent-submission-message';
}
