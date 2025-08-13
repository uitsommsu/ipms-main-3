<?php

namespace App\Filament\Management\Resources\PatentResource\Pages;

use Filament\Tables\Table;
use App\Models\PatentDocument;
use App\Enums\PatentStatusEnum;
use Filament\Infolists\Infolist;
use App\Enums\DocumentStatusEnum;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Infolists\Components\Group;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\SelectColumn;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Management\Resources\PatentResource;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class PatentDocuments extends Page implements HasForms, HasInfolists, HasTable
{
    use InteractsWithRecord;
    use InteractsWithInfolists;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = PatentResource::class;

    protected static string $view = 'filament.management.resources.patent-resource.pages.patent-documents';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load('documents');
        if ($this->record->status === PatentStatusEnum::ABANDONED || $this->record->status === PatentStatusEnum::WITHDRAWN || $this->record->status === PatentStatusEnum::APPROVED) {
            Notification::make()->warning()->title('Cannot view document page')->seconds(.5)->send();
            Redirect::to(PatentResource::getUrl('index'));
        }
    }


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                Section::make('General Information')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Group::make([
                                    TextEntry::make('invention'),
                                    TextEntry::make('inventors'),
                                    Grid::make()
                                        ->schema([
                                            TextEntry::make('patent_number')
                                                ->label('Patent #'),
                                            TextEntry::make('status')
                                                ->badge()
                                                ->color(static function ($record) {
                                                    return match ($record->status) {
                                                        PatentStatusEnum::APPROVED => 'success',
                                                        PatentStatusEnum::FORMALITY_EXAMINATION_REPORT => 'warning',
                                                        PatentStatusEnum::SUBSEQUENT_EXAMINATION_REPORT => 'warning',
                                                        PatentStatusEnum::NOTICE_OF_PUBLICATION => 'warning',
                                                        PatentStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT => 'warning',
                                                        PatentStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE => 'warning',
                                                        PatentStatusEnum::NOTICE_OF_ISSUANCE => 'warning',
                                                        PatentStatusEnum::CLAIMING_OF_CERTIFICATE => 'warning',
                                                        PatentStatusEnum::ISSUANCE_OF_CERTIFICATE => 'warning',
                                                        PatentStatusEnum::SUBMITTED_IPOPHL => 'info',
                                                        PatentStatusEnum::SUBMITTED_UITSO => 'gray',
                                                        PatentStatusEnum::ABANDONED => 'danger',
                                                        PatentStatusEnum::WITHDRAWN => 'danger',
                                                    };
                                                }),
                                        ])


                                ]),

                                Group::make([


                                    TextEntry::make('filing_date')
                                        ->badge()
                                        ->date()
                                        ->color('success'),
                                    TextEntry::make('publication_date')
                                        ->badge()
                                        ->date()
                                        ->color('success'),
                                    TextEntry::make('expiry_date')
                                        ->badge()
                                        ->date()
                                        ->color('success'),

                                ])
                            ])

                    ]),




            ]);
    }



    public function table(Table $table): Table
    {
        return $table
            ->query(PatentDocument::query()->where('patent_id', $this->record->id)->latest())
            ->columns([
                TextColumn::make('document_type')
                    ->label('Type of Document '),

                SelectColumn::make('status')
                    ->options(DocumentStatusEnum::class)
                    ->rules(['required'])
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title('Status Updated')
                            ->body("The status has been updated to: " . DocumentStatusEnum::from($state)->getLabel())
                            ->success()
                            ->send();
                    }),

                TextColumn::make('created_at')
                    ->label('Date Submitted')
                    ->date(),

                TextColumn::make('revision_history')
                    ->label('Revision #')
                    ->formatStateUsing(function (string $state): string {
                        return $state == 0 ? 'Original Document' : 'Revision ' . $state;
                    }),
            ])
            ->actions([
                Action::make('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($record) {
                        return response()->download(storage_path('app/' . $record->filename));
                    }),

                Action::make('Comments')
                    ->icon('heroicon-o-document-duplicate')
                    ->fillForm(fn(PatentDocument $record): array => [
                        'comments' => $record->comments,
                    ])
                    ->form([
                        RichEditor::make('comments')
                            ->label('Provide Comments')
                            ->required(),
                    ])

                    ->action(function (array $data, PatentDocument $record): void {
                        $record->comments = $data['comments'];
                        $record->commented_at = now();
                        $record->save();
                    })

            ]);
    }
}
