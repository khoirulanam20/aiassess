<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-ink">{{ __('Konfigurasi') }}: {{ $orgAssessment->assessment->name }}</h2></x-slot>
    <form method="POST" action="{{ route('admin.assessments.org.update', $orgAssessment) }}" class="card max-w-2xl space-y-4">
        @csrf @method('PATCH')
        <label class="flex items-center gap-2"><input type="checkbox" name="is_enabled" value="1" {{ old('is_enabled', $orgAssessment->is_enabled) ? 'checked' : '' }} class="rounded border-gray-300"><span class="text-sm font-medium">{{ __('Aktifkan untuk perusahaan ini') }}</span></label>
        <div><x-input-label for="custom_name" :value="__('Nama kustom (opsional)')" /><x-text-input id="custom_name" name="custom_name" class="mt-1 block w-full" :value="old('custom_name', $orgAssessment->custom_name)" /></div>
        <div><x-input-label for="cooldown_days" :value="__('Cooldown override (hari, kosongkan = default global)')" /><x-text-input id="cooldown_days" name="cooldown_days" type="number" class="mt-1 block w-full" :value="old('cooldown_days', $orgAssessment->cooldown_days)" /></div>
        <div><x-input-label for="custom_description" :value="__('Deskripsi kustom')" /><textarea id="custom_description" name="custom_description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('custom_description', $orgAssessment->custom_description) }}</textarea></div>
        <div><x-input-label for="custom_instructions" :value="__('Instruksi kustom')" /><textarea id="custom_instructions" name="custom_instructions" rows="3" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('custom_instructions', $orgAssessment->custom_instructions) }}</textarea></div>
        <x-ui.form-actions
            :cancel-href="route('admin.assessments.org.index')"
            :submit-title="__('Simpan')"
        />
    </form>
</x-app-layout>
