<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables\Table;
use App\Models\UtilityModelDocument;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class UtilityModelDocumentTable extends Component implements HasForms, HasTable
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
            ->query(UtilityModelDocument::query()->where('utility_model_id', $this->id)->latest())
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
            ]);
    }

    public function render()
    {
        return view('livewire.utility-model-document-table');
    }
}
