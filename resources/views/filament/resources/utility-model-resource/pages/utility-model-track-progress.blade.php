<x-filament-panels::page>

    {{ $this->infolist }}

    <x-filament::section>
        <x-slot name="heading">
            Tasks
        </x-slot>

        <x-slot name="description">
            Please accomplished the tasks given.
        </x-slot>

        {{ $this->table }}

    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            Documents
        </x-slot>

        <x-slot name="description">
            Documents submitted. Please read the comments
        </x-slot>

        <livewire:utility-model-document-table :id="$this->record->id" />

    </x-filament::section>


    <x-filament::section>
        <x-slot name="heading">
            Application Timeline
        </x-slot>
        <x-filament::card>
            <div class="mt-4 p-4">
                <div class="flex flex-col items-center gap-4">
                    @php

                        $statuses = [
                            \App\Enums\UtilityModelStatusEnum::SUBMITTED_UITSO,
                            \App\Enums\UtilityModelStatusEnum::SUBMITTED_IPOPHL,
                            \App\Enums\UtilityModelStatusEnum::FORMALITY_EXAMINATION_REPORT,
                            \App\Enums\UtilityModelStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT,
                            \App\Enums\UtilityModelStatusEnum::SUBSEQUENT_EXAMINATION_REPORT,
                            \App\Enums\UtilityModelStatusEnum::NOTICE_OF_PUBLICATION,
                            \App\Enums\UtilityModelStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE,
                            \App\Enums\UtilityModelStatusEnum::NOTICE_OF_ISSUANCE,
                            \App\Enums\UtilityModelStatusEnum::CLAIMING_OF_CERTIFICATE,
                            \App\Enums\UtilityModelStatusEnum::ISSUANCE_OF_CERTIFICATE,
                            \App\Enums\UtilityModelStatusEnum::APPROVED,
                        ];
                        $currentStatus = $this->record->status;
                    @endphp

                    @foreach ($statuses as $index => $status)
                        <div class="relative flex flex-col items-center group">
                            <div @class([
                                'w-40 h-16 border-2 rounded-lg flex items-center justify-center p-2 text-center text-sm font-medium transition-all',
                                'bg-success-500 text-white border-success-600' =>
                                    $status === $currentStatus &&
                                    $status === \App\Enums\UtilityModelStatusEnum::APPROVED,
                                'bg-success-500 text-white border-warning-600' =>
                                    $status === $currentStatus &&
                                    str_contains($status->value, 'EXAMINATION'),
                                'bg-info-500 text-white border-info-600' =>
                                    $status === $currentStatus &&
                                    $status === \App\Enums\UtilityModelStatusEnum::SUBMITTED_IPOPHL,
                                'bg-gray-500 text-white border-gray-600' =>
                                    $status === $currentStatus &&
                                    $status === \App\Enums\UtilityModelStatusEnum::SUBMITTED_UITSO,
                                'bg-gray-100 text-gray-600 border-gray-200' => $status !== $currentStatus,
                            ])>
                                <span @class([
                                    'font-bold text-xl text-primary-600 p-4' => $status === $currentStatus,
                                    'text-gray-900' => $status !== $currentStatus,
                                ])>
                                    {{ $status->getLabel() }}
                                </span>
                            </div>

                            @if ($index < count($statuses) - 1)
                                <div class="mt-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </x-filament::card>

    </x-filament::section>

</x-filament-panels::page>
