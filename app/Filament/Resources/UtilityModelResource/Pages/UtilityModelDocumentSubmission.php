<?php

namespace App\Filament\Resources\UtilityModelResource\Pages;

use Filament\Forms\Form;
use App\Enums\DocumentStatusEnum;
use Filament\Resources\Pages\Page;
use App\Models\UtilityModelDocument;
use App\Enums\UtilityModelStatusEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use Filament\Forms\Components\FileUpload;
use App\Enums\UtilityModelDocumentTypeEnum;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\UtilityModelResource;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class UtilityModelDocumentSubmission extends Page implements HasForms
{
    use InteractsWithRecord;
    use InteractsWithForms;
    use WithFileUploads;

    protected static string $resource = UtilityModelResource::class;

    protected static string $view = 'filament.resources.utility-model-resource.pages.utility-model-document-submission';

    public ?array $data = [];

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->form->fill();

        if ($this->record->status === UtilityModelStatusEnum::ABANDONED || $this->record->status === UtilityModelStatusEnum::WITHDRAWN) {
            Notification::make()->warning()->title('Viewing this page is not allowed')->seconds(.5)->send();
            Redirect::to(UtilityModelResource::getUrl('index'));
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('document_type')
                    ->label('Type of Document')
                    ->options(UtilityModelDocumentTypeEnum::class)
                    ->required(),

                FileUpload::make('filename')
                    ->label('Upload File')
                    ->required()
                    ->disk('local')
                    ->directory('documents')
                    ->acceptedFileTypes(['application/pdf'])  // Limit to PDF files
                    ->maxSize(5120)  // Optional: Set max size (e.g., 1MB = 1024KB)
                    ->columnSpanFull(),
                // ...
            ])
            ->statePath('data');
    }

    public function create(): void
    {

        $data = $this->form->getState();

        $utilityModelDocument = UtilityModelDocument::query()
            ->where('utility_model_id', $this->record->id)
            ->where('document_type', $data['document_type'])
            ->latest()
            ->first();

        $revisionNumber = $utilityModelDocument ? $utilityModelDocument->revision_history + 1 : 0;

        $data['utility_model_id'] = $this->record->id;

        $data['status'] = DocumentStatusEnum::UNDER_REVIEW->value;

        $data['revision_history'] = $revisionNumber;

        UtilityModelDocument::create($data);

        Notification::make()
            ->title('Utility Model Document Submitted Successfully')
            ->success()
            ->send();

        // Reset the form
        $this->form->fill();
    }
}
