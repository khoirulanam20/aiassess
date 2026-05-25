@props(['department' => null])

<div>
    <x-input-label for="name" :value="__('Nama departemen')" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $department?->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>
<div>
    <x-input-label for="code" :value="__('Kode (opsional)')" />
    <x-text-input id="code" name="code" class="mt-1 block w-full" :value="old('code', $department?->code)" />
    <x-input-error :messages="$errors->get('code')" class="mt-1" />
</div>
<label class="flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $department?->is_active ?? true))>
    <span class="text-sm text-gray-700">{{ __('Aktif') }}</span>
</label>
