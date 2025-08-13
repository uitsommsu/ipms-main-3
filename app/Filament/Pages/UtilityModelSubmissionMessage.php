<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class UtilityModelSubmissionMessage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static bool $shouldRegisterNavigation = false;

    protected  ?string $heading = 'Utility Model Submission Confirmation';

    protected static string $view = 'filament.pages.utility-model-submission-message';
}
