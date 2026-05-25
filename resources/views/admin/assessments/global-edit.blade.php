<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-ink">{{ __('Edit Assessment Global') }}: {{ $assessment->name }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.system.assessments.update', $assessment) }}" class="space-y-8">
        @csrf
        @method('PATCH')

        <section class="card max-w-2xl space-y-4">
            <h3 class="text-lg font-semibold text-ink">{{ __('Metadata') }}</h3>
            <div>
                <x-input-label for="name" :value="__('Nama')" />
                <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $assessment->name)" required />
            </div>
            <div>
                <x-input-label for="type" :value="__('Tipe')" />
                <select id="type" name="type" class="mt-1 block w-full rounded-lg border-gray-300">
                    <option value="free" @selected(old('type', $assessment->type) === 'free')>free</option>
                    <option value="premium" @selected(old('type', $assessment->type) === 'premium')>premium</option>
                </select>
            </div>
            <div>
                <x-input-label for="description" :value="__('Deskripsi')" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('description', $assessment->description) }}</textarea>
            </div>
            <div>
                <x-input-label for="instructions" :value="__('Instruksi')" />
                <textarea id="instructions" name="instructions" rows="3" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('instructions', $assessment->instructions) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('Jumlah soal (otomatis)')" />
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $questionBundle['mode'] === 'agility'
                            ? collect($questionBundle['groups'])->sum(fn ($g) => count($g['questions']))
                            : count($questionBundle['questions']) }}
                        {{ __('soal tersimpan') }} — diperbarui saat simpan.
                    </p>
                </div>
                <div>
                    <x-input-label for="cooldown_days" :value="__('Cooldown (hari)')" />
                    <x-text-input id="cooldown_days" name="cooldown_days" type="number" class="mt-1 block w-full" :value="old('cooldown_days', $assessment->cooldown_days)" required />
                </div>
            </div>
            <div>
                <x-input-label for="sort_order" :value="__('Urutan')" />
                <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full" :value="old('sort_order', $assessment->sort_order)" required />
            </div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $assessment->is_active) ? 'checked' : '' }} class="rounded border-gray-300">
                <span class="text-sm">{{ __('Aktif (global)') }}</span>
            </label>
        </section>

        <section class="card space-y-4">
            <div>
                <h3 class="text-lg font-semibold text-ink">{{ __('Daftar pertanyaan') }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Superadmin dapat mengubah teks pertanyaan dan opsi jawaban. Perubahan disimpan ke file JSON di storage.') }}
                    @if (! empty($questionBundle['file'] ?? null))
                        <span class="block font-mono text-xs text-gray-500">{{ $questionBundle['file'] }}</span>
                    @endif
                </p>
            </div>

            @if ($questionBundle['mode'] === 'agility')
                <div class="space-y-8">
                    @foreach ($questionBundle['groups'] as $group)
                        <div class="rounded-xl border border-gray-200 p-4">
                            <h4 class="mb-3 font-semibold text-ink">{{ $group['label'] }}</h4>
                            <p class="mb-4 font-mono text-xs text-gray-500">{{ $group['file'] }}</p>
                            @include('admin.assessments.partials.question-editor', [
                                'namePrefix' => 'groups',
                                'groupKey' => $group['key'],
                                'questions' => $group['questions'],
                                'showDimension' => false,
                            ])
                        </div>
                    @endforeach
                </div>
            @elseif ($questionBundle['mode'] === 'single')
                @include('admin.assessments.partials.question-editor', [
                    'namePrefix' => 'questions',
                    'questions' => $questionBundle['questions'],
                    'showDimension' => $showDimension,
                ])
            @else
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    {{ __('Assessment ini tidak memiliki bank soal JSON (mis. fingerprint, video). Metadata tetap dapat diedit.') }}
                </div>
            @endif
        </section>

        <x-ui.form-actions
            :cancel-href="route('admin.system.assessments.index')"
            :submit-title="__('Simpan perubahan')"
        />
    </form>
</x-app-layout>
