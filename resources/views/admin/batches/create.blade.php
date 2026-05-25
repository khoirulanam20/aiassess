<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-ink">{{ __('Buat Batch Assessment') }}</h2>
            <x-ui.button-icon :href="route('admin.batches.index')" variant="ghost" icon="arrow-left">
                {{ __('Daftar batch') }}
            </x-ui.button-icon>
        </div>
    </x-slot>

    <form
        method="POST"
        action="{{ route('admin.batches.store') }}"
        class="mx-auto max-w-6xl space-y-6"
        x-data="batchForm()"
    >
        @csrf

        <div class="grid gap-6 lg:grid-cols-5">
            <section class="card space-y-4 lg:col-span-2">
                <h3 class="font-semibold text-ink">{{ __('Informasi Batch') }}</h3>
                <div>
                    <x-input-label for="name" :value="__('Nama batch')" />
                    <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('description') }}</textarea>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <x-input-label for="starts_at" :value="__('Mulai (opsional)')" />
                        <x-text-input id="starts_at" name="starts_at" type="datetime-local" class="mt-1 block w-full text-sm" :value="old('starts_at')" />
                    </div>
                    <div>
                        <x-input-label for="ends_at" :value="__('Berakhir (opsional)')" />
                        <x-text-input id="ends_at" name="ends_at" type="datetime-local" class="mt-1 block w-full text-sm" :value="old('ends_at')" />
                    </div>
                </div>
            </section>

            <section class="card space-y-3 lg:col-span-3">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="font-semibold text-ink">{{ __('Pilih Assessment') }}</h3>
                    @if ($assessments->isNotEmpty())
                        <x-ui.button-icon
                            type="button"
                            variant="ghost"
                            icon="squares"
                            class="!h-8 !w-auto !px-2"
                            @click="toggleAllAssessments()"
                        >
                            <span class="text-xs" x-text="allAssessmentsSelected ? '{{ __('Batalkan semua') }}' : '{{ __('Pilih semua') }}'"></span>
                        </x-ui.button-icon>
                    @endif
                </div>
                @if ($assessments->isEmpty())
                    <p class="text-sm text-amber-700">{{ __('Tidak ada assessment aktif untuk perusahaan. Aktifkan di menu Konfig Assessment.') }}</p>
                @else
                    <div class="grid max-h-[280px] gap-2 overflow-y-auto pr-1 sm:grid-cols-2">
                        @foreach ($assessments as $assessment)
                            <label
                                class="flex cursor-pointer items-start gap-2.5 rounded-lg border p-2.5 transition-colors"
                                :class="selectedAssessments.includes('{{ $assessment->id }}') ? 'border-sky-400 bg-sky-50/50' : 'border-gray-200 hover:border-sky-200'"
                            >
                                <input
                                    type="checkbox"
                                    name="assessment_ids[]"
                                    value="{{ $assessment->id }}"
                                    class="mt-0.5 rounded border-gray-300"
                                    x-model="selectedAssessments"
                                    @checked(in_array($assessment->id, old('assessment_ids', [])))
                                >
                                <span class="min-w-0">
                                    <span class="block text-sm font-medium leading-tight text-ink">{{ $assessment->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $assessment->slug }} · {{ $assessment->question_count }} {{ __('soal') }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
                <x-input-error :messages="$errors->get('assessment_ids')" class="mt-1" />
            </section>
        </div>

        <section class="card space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-semibold text-ink">{{ __('Assign Kandidat / Karyawan') }}</h3>
                @if ($candidates->isNotEmpty())
                    <x-ui.button-icon
                        type="button"
                        variant="ghost"
                        icon="squares"
                        class="!h-8 !w-auto !px-2"
                        @click="toggleAllFilteredCandidates()"
                    >
                        <span class="text-xs" x-text="allFilteredSelected ? '{{ __('Batalkan semua') }}' : '{{ __('Pilih semua') }}'"></span>
                    </x-ui.button-icon>
                @endif
            </div>

            @if ($candidates->isEmpty())
                <p class="text-sm text-amber-700">{{ __('Belum ada kandidat. Tambahkan di menu Kandidat / Karyawan.') }}</p>
            @else
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="sm:col-span-2 lg:col-span-2">
                        <label class="text-xs font-medium text-gray-500">{{ __('Cari') }}</label>
                        <div class="relative mt-1">
                            <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <input
                                type="search"
                                x-model="searchQuery"
                                placeholder="{{ __('Nama atau email...') }}"
                                class="block w-full rounded-lg border-gray-300 py-2 pl-9 pr-3 text-sm"
                            >
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500">{{ __('Departemen') }}</label>
                        <select
                            x-model="filterDepartmentId"
                            @change="onFilterDepartmentChange()"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                        >
                            <option value="">{{ __('Semua departemen') }}</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500">{{ __('Posisi') }}</label>
                        <select
                            x-model="filterPositionId"
                            :disabled="!filterDepartmentId"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm disabled:bg-gray-100"
                        >
                            <option value="">{{ __('Semua posisi') }}</option>
                            <template x-for="pos in filterPositions" :key="pos.id">
                                <option :value="pos.id" x-text="pos.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <p class="text-xs text-gray-500">
                    <span x-text="filteredCandidates.length"></span> {{ __('kandidat ditampilkan') }}
                    <span x-show="filterDepartmentId || filterPositionId || searchQuery" x-cloak>· <button type="button" class="text-sky-600 hover:underline" @click="resetFilters()">{{ __('Reset filter') }}</button></span>
                </p>

                <div class="max-h-80 overflow-y-auto rounded-lg border border-gray-200 divide-y divide-gray-100">
                    <template x-for="candidate in candidates" :key="candidate.id">
                        <label
                            class="flex cursor-pointer items-center gap-3 px-4 py-3 hover:bg-gray-50"
                            x-show="isCandidateVisible(candidate)"
                            x-cloak
                        >
                            <input
                                type="checkbox"
                                name="user_ids[]"
                                :value="candidate.id"
                                class="rounded border-gray-300"
                                x-model="selectedCandidates"
                            >
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sky-100 text-xs font-semibold text-sky-700" x-text="candidate.initials"></span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-medium text-ink" x-text="candidate.name"></span>
                                <span class="block truncate text-xs text-gray-500" x-text="candidate.subtitle"></span>
                            </span>
                        </label>
                    </template>
                    <p
                        x-show="filteredCandidates.length === 0"
                        x-cloak
                        class="px-4 py-8 text-center text-sm text-gray-500"
                    >
                        {{ __('Tidak ada kandidat yang cocok dengan filter.') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-600">
                    <span>
                        <span x-text="selectedAssessments.length"></span> assessment ×
                        <span x-text="selectedCandidates.length"></span> kandidat
                    </span>
                    <strong class="text-ink">
                        <span x-text="selectedAssessments.length * selectedCandidates.length"></span> {{ __('penugasan') }}
                    </strong>
                </div>
            @endif
            <x-input-error :messages="$errors->get('user_ids')" class="mt-1" />
        </section>

        <x-ui.form-actions
            :cancel-href="route('admin.batches.index')"
            :submit-title="__('Buat Batch')"
            :submit-disabled="$assessments->isEmpty() || $candidates->isEmpty()"
        />
    </form>

    @php
        $batchFormCandidates = $candidates->map(function ($c) {
            $initials = collect(explode(' ', $c->name))
                ->filter()
                ->take(2)
                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                ->join('');

            return [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'department_id' => $c->userDetail?->department_id,
                'position_id' => $c->userDetail?->position_id,
                'initials' => $initials,
                'subtitle' => trim(collect([
                    $c->email,
                    $c->userDetail?->departmentLabel(),
                    $c->userDetail?->positionLabel(),
                ])->filter()->join(' · ')),
            ];
        })->values();

        $batchFormPositions = $positions->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'department_id' => $p->department_id,
        ])->values();
    @endphp

    @push('scripts')
        <script>
            function batchForm() {
                const assessmentIds = @json($assessments->pluck('id'));
                const oldAssessments = @json(old('assessment_ids', []));
                const oldCandidates = @json(old('user_ids', []));

                const candidates = @json($batchFormCandidates);
                const allPositions = @json($batchFormPositions);

                return {
                    selectedAssessments: oldAssessments.length ? oldAssessments.map(String) : [],
                    selectedCandidates: oldCandidates.length ? oldCandidates.map(String) : [],
                    searchQuery: '',
                    filterDepartmentId: '',
                    filterPositionId: '',
                    allPositions,
                    candidates,
                    get allAssessmentsSelected() {
                        return assessmentIds.length > 0 && this.selectedAssessments.length === assessmentIds.length;
                    },
                    get filterPositions() {
                        if (!this.filterDepartmentId) {
                            return [];
                        }
                        return this.allPositions.filter(
                            (p) => String(p.department_id) === String(this.filterDepartmentId)
                        );
                    },
                    get filteredCandidates() {
                        return this.candidates.filter((c) => this.isCandidateVisible(c));
                    },
                    isCandidateVisible(candidate) {
                        const q = this.searchQuery.trim().toLowerCase();

                        if (this.filterDepartmentId && String(candidate.department_id) !== String(this.filterDepartmentId)) {
                            return false;
                        }
                        if (this.filterPositionId && String(candidate.position_id) !== String(this.filterPositionId)) {
                            return false;
                        }
                        if (q && !candidate.name.toLowerCase().includes(q) && !candidate.email.toLowerCase().includes(q)) {
                            return false;
                        }
                        return true;
                    },
                    get allFilteredSelected() {
                        const visible = this.filteredCandidates.map((c) => String(c.id));
                        return visible.length > 0 && visible.every((id) => this.selectedCandidates.includes(id));
                    },
                    toggleAllAssessments() {
                        this.selectedAssessments = this.allAssessmentsSelected
                            ? []
                            : assessmentIds.map(String);
                    },
                    toggleAllFilteredCandidates() {
                        const visibleIds = this.filteredCandidates.map((c) => String(c.id));
                        if (this.allFilteredSelected) {
                            this.selectedCandidates = this.selectedCandidates.filter((id) => !visibleIds.includes(id));
                        } else {
                            visibleIds.forEach((id) => {
                                if (!this.selectedCandidates.includes(id)) {
                                    this.selectedCandidates.push(id);
                                }
                            });
                        }
                    },
                    onFilterDepartmentChange() {
                        this.filterPositionId = '';
                    },
                    resetFilters() {
                        this.searchQuery = '';
                        this.filterDepartmentId = '';
                        this.filterPositionId = '';
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
