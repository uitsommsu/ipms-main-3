<?php

namespace App\Livewire;

use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Table;
use App\Models\PatentDocument;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class PatentDocumentTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public int $id;

    public function mount($id)
    {
        $this->id = $id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(PatentDocument::query()->where('patent_id', $this->id)->latest())
            ->columns([
                TextColumn::make('document_type')
                    ->label('Type of Document'),

                TextColumn::make('created_at')
                    ->label('Date  Submitted')
                    ->date('F j, Y'),

                TextColumn::make('comments')
                    ->label('Comments')
                    ->wrap()
                    ->html(),

                TextColumn::make('commented_at')
                    ->label('Date Commented')
                    ->date('F j, Y'),

                TextColumn::make('revision_history')
                    ->label('Revision #')
                    ->formatStateUsing(function (string $state): string {
                        return $state == 0 ? 'Original Document' : 'Revision ' . $state;
                    }),

            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.patent-document-table');
    }
}
