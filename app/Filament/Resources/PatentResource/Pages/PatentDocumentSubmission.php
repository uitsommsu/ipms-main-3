<?php

namespace App\Filament\Resources\PatentResource\Pages;

use Filament\Forms\Form;
use App\Models\PatentDocument;
use App\Enums\PatentStatusEnum;
use App\Enums\DocumentStatusEnum;
use Filament\Resources\Pages\Page;
use App\Enums\PatentDocumentTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Facades\Redirect;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\PatentResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class PatentDocumentSubmission extends Page implements HasForms
{
    use InteractsWithRecord;
    use InteractsWithForms;
    use WithFileUploads;

    protected static string $resource = PatentResource::class;

    protected static string $view = 'filament.resources.patent-resource.pages.patent-document-submission';

    public ?array $data = [];

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->form->fill();

        if ($this->record->status === PatentStatusEnum::ABANDONED || $this->record->status === PatentStatusEnum::WITHDRAWN) {
            Notification::make()->warning()->title('You are not allowed to view this page')->seconds(.5)->send();
            Redirect::to(PatentResource::getUrl('index'));
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('document_type')
                    ->label('Type of Document')
                    ->options(PatentDocumentTypeEnum::class)
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

        $patentDocument = PatentDocument::query()
            ->where('patent_id', $this->record->id)
            ->where('document_type', $data['document_type'])
            ->latest()
            ->first();

        $revisionNumber = $patentDocument ? $patentDocument->revision_history + 1 : 0;

        $data['patent_id'] = $this->record->id;

        $data['status'] = DocumentStatusEnum::UNDER_REVIEW->value;

        $data['revision_history'] = $revisionNumber;

        PatentDocument::create($data);

        Notification::make()
            ->title('Patent Document Submitted Successfully')
            ->success()
            ->send();

        // Reset the form
        $this->form->fill();
    }
}
