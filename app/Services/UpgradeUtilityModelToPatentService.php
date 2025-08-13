<?php

namespace App\Services;

use App\Models\Patent;
use App\Models\UtilityModel;
use App\Enums\PatentStatusEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\PatentDocumentTypeEnum;
use App\Enums\UtilityModelStatusEnum;
use App\Enums\UtilityModelDocumentTypeEnum;

class UpgradeUtilityModelToPatentService
{
    public static function handle(UtilityModel $utilityModel): Patent
    {
        return DB::transaction(function () use ($utilityModel) {

            // Map the status enum
            $status = self::mapStatus($utilityModel->status);
            Patent::unsetEventDispatcher();

            // Create patent from utility model
            $patent = Patent::create([
                'invention' => $utilityModel->title,
                'slug' => $utilityModel->slug,
                'inventors' => $utilityModel->researchers,
                'description' => $utilityModel->description,
                'filing_date' => $utilityModel->filing_date,
                'publication_date' => $utilityModel->publication_date,
                'expiry_date' => $utilityModel->expiry_date,
                'status' => $status, // Assuming status can be directly mapped
                'images' => $utilityModel->images, // Assuming images can be directly mapped
                'original_utility_model_id' => $utilityModel->id,
                'created_at' => $utilityModel->created_at,
                'updated_at' => $utilityModel->updated_at,
            ]);

            foreach ($utilityModel->proponents as $proponent) {
                $patent->proponents()->attach([
                    'user_id' => $proponent->id,
                ]);
            }

            foreach ($utilityModel->tasks as $task) {
                Patent::unsetEventDispatcher();
                $patent->tasks()->create([
                    'patent_id' => $patent->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'due_at' => $task->due_at,
                    'status' => $task->status,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                ]);
            }

            foreach ($utilityModel->documents as $document) {
                $documentType = self::mapDocumentType($document->document_type);
                $patent->documents()->create([
                    'utility_model_id' => $patent->id,
                    'document_type' => $documentType,
                    'filename' => $document->filename,
                    'status' => $document->status,
                    'comments' => $document->comments,
                    'commented_at' => $document->commented_at,
                    'revision_hinstory' => $document->revision_history,
                    'created_at' => $document->created_at,
                    'updated_at' => $document->updated_at,
                ]);
            }

            $utilityModel->update([
                'upgraded_to_patent_at' => now(),
            ]);

            return $patent;
        });
    }

    protected static function mapStatus(?UtilityModelStatusEnum $status): PatentStatusEnum
    {
        return match ($status) {
            UtilityModelStatusEnum::SUBMITTED_UITSO => PatentStatusEnum::SUBMITTED_UITSO,
            UtilityModelStatusEnum::SUBMITTED_IPOPHL => PatentStatusEnum::SUBMITTED_IPOPHL,
            UtilityModelStatusEnum::FORMALITY_EXAMINATION_REPORT => PatentStatusEnum::FORMALITY_EXAMINATION_REPORT,
            UtilityModelStatusEnum::SUBSEQUENT_EXAMINATION_REPORT => PatentStatusEnum::SUBSEQUENT_EXAMINATION_REPORT,
            UtilityModelStatusEnum::NOTICE_OF_PUBLICATION => PatentStatusEnum::NOTICE_OF_PUBLICATION,
            UtilityModelStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT => PatentStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT,
            UtilityModelStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE => PatentStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE,
            UtilityModelStatusEnum::NOTICE_OF_ISSUANCE => PatentStatusEnum::NOTICE_OF_ISSUANCE,
            UtilityModelStatusEnum::CLAIMING_OF_CERTIFICATE => PatentStatusEnum::CLAIMING_OF_CERTIFICATE,
            UtilityModelStatusEnum::ISSUANCE_OF_CERTIFICATE => PatentStatusEnum::ISSUANCE_OF_CERTIFICATE,
            UtilityModelStatusEnum::APPROVED => PatentStatusEnum::APPROVED,

            default => PatentStatusEnum::SUBMITTED_UITSO,
        };
    }

    protected static function mapDocumentType(?UtilityModelDocumentTypeEnum $documentType): PatentDocumentTypeEnum
    {
        return match ($documentType) {
            UtilityModelDocumentTypeEnum::DISCLOSURE_FORM => PatentDocumentTypeEnum::DISCLOSURE_FORM,
            UtilityModelDocumentTypeEnum::CLAIMS => PatentDocumentTypeEnum::CLAIMS,
            UtilityModelDocumentTypeEnum::SPECIFICATION => PatentDocumentTypeEnum::SPECIFICATION,
            UtilityModelDocumentTypeEnum::ABSTRACT => PatentDocumentTypeEnum::ABSTRACT,
            UtilityModelDocumentTypeEnum::FORMALITY_EXAMINATION_REPORT => PatentDocumentTypeEnum::FORMALITY_EXAMINATION_REPORT,
            UtilityModelDocumentTypeEnum::SUBSEQUENT_FORMALITY_EXAMINATION_REPORT => PatentDocumentTypeEnum::SUBSEQUENT_FORMALITY_EXAMINATION_REPORT,
            UtilityModelDocumentTypeEnum::OTHER_IP_DOCUMENT => PatentDocumentTypeEnum::OTHER_IP_DOCUMENT,

            default => PatentDocumentTypeEnum::DISCLOSURE_FORM,
        };
    }
}
