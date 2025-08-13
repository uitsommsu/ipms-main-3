<?php

namespace App\Filament\Management\Resources\UtilityModelResource\Pages;

use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use App\Enums\DocumentStatusEnum;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use App\Models\UtilityModelDocument;
use App\Enums\UtilityModelStatusEnum;
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
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use App\Filament\Management\Resources\UtilityModelResource;

class UtilityModelDocuments extends Page implements HasForms, HasInfolists, HasTable
{
    use InteractsWithRecord;
    use InteractsWithInfolists;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = UtilityModelResource::class;

    protected static string $view = 'filament.management.resources.utility-model-resource.pages.utility-model-documents';



    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load('documents');
        if ($this->record->status === UtilityModelStatusEnum::ABANDONED || $this->record->status === UtilityModelStatusEnum::WITHDRAWN || $this->record->status === UtilityModelStatusEnum::APPROVED) {
            Notification::make()->warning()->title('Cannot view document page')->seconds(.5)->send();
            Redirect::to(UtilityModelResource::getUrl('index'));
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
                                    TextEntry::make('title'),
                                    TextEntry::make('researchers'),
                                    Grid::make()
                                        ->schema([
                                            TextEntry::make('um_number')
                                                ->label('UM #'),
                                            TextEntry::make('status')
                                                ->badge()
                                                ->color(static function ($record) {
                                                    return match ($record->status) {
                                                        UtilityModelStatusEnum::APPROVED => 'success',
                                                        UtilityModelStatusEnum::FORMALITY_EXAMINATION_REPORT => 'warning',
                                                        UtilityModelStatusEnum::SUBSEQUENT_EXAMINATION_REPORT => 'warning',
                                                        UtilityModelStatusEnum::NOTICE_OF_PUBLICATION => 'warning',
                                                        UtilityModelStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT => 'warning',
                                                        UtilityModelStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE => 'warning',
                                                        UtilityModelStatusEnum::NOTICE_OF_ISSUANCE => 'warning',
                                                        UtilityModelStatusEnum::CLAIMING_OF_CERTIFICATE => 'warning',
                                                        UtilityModelStatusEnum::ISSUANCE_OF_CERTIFICATE => 'warning',
                                                        UtilityModelStatusEnum::SUBMITTED_IPOPHL => 'info',
                                                        UtilityModelStatusEnum::SUBMITTED_UITSO => 'gray',
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
            ->query(UtilityModelDocument::query()->where('utility_model_id', $this->record->id)->latest())
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
                    ->fillForm(fn(UtilityModelDocument $record): array => [
                        'comments' => $record->comments,
                    ])
                    ->form([
                        RichEditor::make('comments')
                            ->label('Provide Comments')
                            ->required(),
                    ])

                    ->action(function (array $data, UtilityModelDocument $record): void {
                        $record->comments = $data['comments'];
                        $record->commented_at = now();
                        $record->save();
                    })

            ]);
    }
}
