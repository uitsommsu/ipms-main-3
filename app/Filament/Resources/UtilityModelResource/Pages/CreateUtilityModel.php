<?php

namespace App\Filament\Resources\UtilityModelResource\Pages;

use App\Enums\DocumentStatusEnum;
use App\Enums\UtilityModelDocumentTypeEnum;
use App\Filament\Pages\UtilityModelSubmissionMessage;
use Throwable;
use Filament\Actions;
use Filament\Support\Exceptions\Halt;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UtilityModelResource;
use function Filament\Support\is_app_url;
use Filament\Support\Facades\FilamentView;

class CreateUtilityModel extends CreateRecord
{
    protected static string $resource = UtilityModelResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return UtilityModelSubmissionMessage::getUrl();
    }

    protected function afterCreate(): void
    {
       $this->record->proponents()->attach(auth()->id());
    }

    public function create(bool $another = false): void
    {
        $this->authorizeAccess();

        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeCreate($data);

            $this->callHook('beforeCreate');

            $this->record = $this->handleRecordCreation($data);

            $this->form->model($this->getRecord())->saveRelationships();

            //save the document
            $document = [
              'document_type' => UtilityModelDocumentTypeEnum::DISCLOSURE_FORM->value,
              'filename'=>$data['document'],
              'status'=>DocumentStatusEnum::UNDER_REVIEW->value,
            ];

            //dd($this->record);

            $this->getRecord()->documents()->create($document);

            $this->callHook('afterCreate');

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->rememberData();

        $this->getCreatedNotification()?->send();

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $this->form->model($this->getRecord()::class);
            $this->record = null;

            $this->fillForm();

            return;
        }

        $redirectUrl = $this->getRedirectUrl();

        $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
    }

}
