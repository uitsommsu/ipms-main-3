<?php

namespace App\Services;

use App\Enums\PatentDocumentTypeEnum;
use App\Models\Patent;
use App\Models\UtilityModel;
use App\Enums\PatentStatusEnum;
use App\Enums\UtilityModelDocumentTypeEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\UtilityModelStatusEnum;
use Dflydev\DotAccessData\Util;

class DowngradePatentToUtilityModelService
{
    public static function handle(Patent $patent): UtilityModel
    {
        return DB::transaction(function () use ($patent) {

            //Map the status enum
            $status = self::mapStatus($patent->status);
            UtilityModel::unsetEventDispatcher();
            // Create utility model from patent
            $utilityModel = UtilityModel::create([
                'title' => $patent->invention,
                'slug' => $patent->slug,
                'researchers' => $patent->inventors,
                'description' => $patent->description,
                'filing_date' => $patent->filing_date,
                'publication_date' => $patent->publication_date,
                'expiry_date' => $patent->expiry_date,
                'status' => $status, // Assuming status can be directly mapped
                'images' => $patent->images, // Assuming images can be directly mapped
                'original_patent_id' => $patent->id,
                'created_at' => $patent->created_at,
                'updated_at' => $patent->updated_at,
            ]);

            foreach ($patent->proponents as $proponent) {
                //dd($proponent);
                $utilityModel->proponents()->attach([
                    'user_id' => $proponent->id,
                ]);
            }

            foreach ($patent->tasks as $task) {
                $utilityModel::unsetEventDispatcher();
                $utilityModel->tasks()->create([
                    'utility_model_id' => $utilityModel->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'due_at' => $task->due_at,
                    'status' => $task->status,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                ]);
            }

            foreach ($patent->documents as $document) {
                $documentType = self::mapDocumentType($document->document_type);
                $utilityModel->documents()->create([
                    'utility_model_id' => $utilityModel->id,
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

            $patent->update([
                'downgraded_to_utility_model_at' => now(),
            ]);

            return $utilityModel;
        });
    }

    protected static function mapStatus(?PatentStatusEnum $status): UtilityModelStatusEnum
    {
        return match ($status) {
            PatentStatusEnum::SUBMITTED_UITSO => UtilityModelStatusEnum::SUBMITTED_UITSO,
            PatentStatusEnum::SUBMITTED_IPOPHL => UtilityModelStatusEnum::SUBMITTED_IPOPHL,
            PatentStatusEnum::FORMALITY_EXAMINATION_REPORT => UtilityModelStatusEnum::FORMALITY_EXAMINATION_REPORT,
            PatentStatusEnum::SUBSEQUENT_EXAMINATION_REPORT => UtilityModelStatusEnum::SUBSEQUENT_EXAMINATION_REPORT,
            PatentStatusEnum::NOTICE_OF_PUBLICATION => UtilityModelStatusEnum::NOTICE_OF_PUBLICATION,
            PatentStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT => UtilityModelStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT,
            PatentStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE => UtilityModelStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE,
            PatentStatusEnum::NOTICE_OF_ISSUANCE => UtilityModelStatusEnum::NOTICE_OF_ISSUANCE,
            PatentStatusEnum::CLAIMING_OF_CERTIFICATE => UtilityModelStatusEnum::CLAIMING_OF_CERTIFICATE,
            PatentStatusEnum::ISSUANCE_OF_CERTIFICATE => UtilityModelStatusEnum::ISSUANCE_OF_CERTIFICATE,
            PatentStatusEnum::APPROVED => UtilityModelStatusEnum::APPROVED,

            default => UtilityModelStatusEnum::SUBMITTED_UITSO,
        };
    }

    protected static function mapDocumentType(?PatentDocumentTypeEnum $documentType): UtilityModelDocumentTypeEnum
    {
        return match ($documentType) {
            PatentDocumentTypeEnum::DISCLOSURE_FORM => UtilityModelDocumentTypeEnum::DISCLOSURE_FORM,
            PatentDocumentTypeEnum::CLAIMS => UtilityModelDocumentTypeEnum::CLAIMS,
            PatentDocumentTypeEnum::SPECIFICATION => UtilityModelDocumentTypeEnum::SPECIFICATION,
            PatentDocumentTypeEnum::ABSTRACT => UtilityModelDocumentTypeEnum::ABSTRACT,
            PatentDocumentTypeEnum::FORMALITY_EXAMINATION_REPORT => UtilityModelDocumentTypeEnum::FORMALITY_EXAMINATION_REPORT,
            PatentDocumentTypeEnum::SUBSEQUENT_FORMALITY_EXAMINATION_REPORT => UtilityModelDocumentTypeEnum::SUBSEQUENT_FORMALITY_EXAMINATION_REPORT,
            PatentDocumentTypeEnum::OTHER_IP_DOCUMENT => UtilityModelDocumentTypeEnum::OTHER_IP_DOCUMENT,

            default => UtilityModelDocumentTypeEnum::DISCLOSURE_FORM,
        };
    }
}
