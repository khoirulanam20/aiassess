<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-ink">{{ __('Buat Batch Assessment') }}</h2>
    </x-slot>

    <form
        method="POST"
        action="{{ route('admin.batches.store') }}"
        class="space-y-6"
        x-data="batchForm()"
    >
        @csrf

        <section class="card max-w-3xl space-y-4">
            <h3 class="font-semibold text-ink">{{ __('Informasi Batch') }}</h3>
            <div>
                <x-input-label for="name" :value="__('Nama batch')" />
                <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('description') }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="starts_at" :value="__('Mulai (opsional)')" />
                    <x-text-input id="starts_at" name="starts_at" type="datetime-local" class="mt-1 block w-full" :value="old('starts_at')" />
                </div>
                <div>
                    <x-input-label for="ends_at" :value="__('Berakhir (opsional)')" />
                    <x-text-input id="ends_at" name="ends_at" type="datetime-local" class="mt-1 block w-full" :value="old('ends_at')" />
                </div>
            </div>
        </section>

        <section class="card space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-ink">{{ __('Pilih Assessment') }}</h3>
                <x-ui.button-icon
                    type="button"
                    variant="ghost"
                    icon="squares"
                    @click="toggleAllAssessments()"
                >
                    <span x-text="allAssessmentsSelected ? '{{ __('Batalkan semua') }}' : '{{ __('Pilih semua') }}'"></span>
                </x-ui.button-icon>
            </div>
            @if ($assessments->isEmpty())
                <p class="text-sm text-amber-700">{{ __('Tidak ada assessment aktif untuk perusahaan. Aktifkan di menu Konfig Assessment.') }}</p>
            @else
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($assessments as $assessment)
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 hover:border-sky-300">
                            <input
                                type="checkbox"
                                name="assessment_ids[]"
                                value="{{ $assessment->id }}"
                                class="mt-1 rounded border-gray-300"
                                x-model="selectedAssessments"
                                @checked(in_array($assessment->id, old('assessment_ids', [])))
                            >
                            <span>
                                <span class="block text-sm font-medium text-ink">{{ $assessment->name }}</span>
                                <span class="text-xs text-gray-500">{{ $assessment->slug }} · {{ $assessment->question_count }} {{ __('soal') }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif
            <x-input-error :messages="$errors->get('assessment_ids')" class="mt-1" />
        </section>

        <section class="card space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-ink">{{ __('Assign Kandidat / Karyawan') }}</h3>
                @if ($candidates->isNotEmpty())
                    <x-ui.button-icon
                        type="button"
                        variant="ghost"
                        icon="squares"
                        @click="toggleAllCandidates()"
                    >
                        <span x-text="allCandidatesSelected ? '{{ __('Batalkan semua') }}' : '{{ __('Pilih semua') }}'"></span>
                    </x-ui.button-icon>
                @endif
            </div>
            @if ($candidates->isEmpty())
                <p class="text-sm text-amber-700">{{ __('Belum ada kandidat. Tambahkan di menu Kandidat / Karyawan.') }}</p>
            @else
                <div class="max-h-96 overflow-y-auto rounded-lg border border-gray-200 divide-y divide-gray-100">
                    @foreach ($candidates as $candidate)
                        <label class="flex cursor-pointer items-center gap-3 px-4 py-3 hover:bg-gray-50">
                            <input
                                type="checkbox"
                                name="user_ids[]"
                                value="{{ $candidate->id }}"
                                class="rounded border-gray-300"
                                x-model="selectedCandidates"
                                @checked(in_array($candidate->id, old('user_ids', [])))
                            >
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-medium text-ink">{{ $candidate->name }}</span>
                                <span class="text-xs text-gray-500">{{ $candidate->email }}
                                    @if ($candidate->userDetail?->department)
                                        · {{ $candidate->userDetail->department }}
                                    @endif
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <p class="text-sm text-gray-500">
                    <span x-text="selectedAssessments.length"></span> assessment ×
                    <span x-text="selectedCandidates.length"></span> kandidat =
                    <strong x-text="selectedAssessments.length * selectedCandidates.length"></strong> penugasan
                </p>
            @endif
            <x-input-error :messages="$errors->get('user_ids')" class="mt-1" />
        </section>

        <x-ui.form-actions
            :cancel-href="route('admin.batches.index')"
            :submit-title="__('Buat Batch')"
            :submit-disabled="$assessments->isEmpty() || $candidates->isEmpty()"
        />
    </form>

    @push('scripts')
        <script>
            function batchForm() {
                const assessmentIds = @json($assessments->pluck('id'));
                const candidateIds = @json($candidates->pluck('id'));
                const oldAssessments = @json(old('assessment_ids', []));
                const oldCandidates = @json(old('user_ids', []));

                return {
                    selectedAssessments: oldAssessments.length ? oldAssessments.map(String) : [],
                    selectedCandidates: oldCandidates.length ? oldCandidates.map(String) : [],
                    get allAssessmentsSelected() {
                        return assessmentIds.length > 0 && this.selectedAssessments.length === assessmentIds.length;
                    },
                    get allCandidatesSelected() {
                        return candidateIds.length > 0 && this.selectedCandidates.length === candidateIds.length;
                    },
                    toggleAllAssessments() {
                        this.selectedAssessments = this.allAssessmentsSelected
                            ? []
                            : assessmentIds.map(String);
                    },
                    toggleAllCandidates() {
                        this.selectedCandidates = this.allCandidatesSelected
                            ? []
                            : candidateIds.map(String);
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
