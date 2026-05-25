@props(['departments', 'position' => null])

<div>
    <x-input-label for="name" :value="__('Nama posisi')" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $position?->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>
<div>
    <x-input-label for="department_id" :value="__('Departemen (opsional)')" />
    <select id="department_id" name="department_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">
        <option value="">{{ __('— Tanpa departemen —') }}</option>
        @foreach ($departments as $department)
            <option
                value="{{ $department->id }}"
                @selected((string) old('department_id', $position?->department_id ?? '') === (string) $department->id)
            >
                {{ $department->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('department_id')" class="mt-1" />
</div>
<div>
    <x-input-label for="code" :value="__('Kode (opsional)')" />
    <x-text-input id="code" name="code" class="mt-1 block w-full" :value="old('code', $position?->code)" />
    <x-input-error :messages="$errors->get('code')" class="mt-1" />
</div>
<label class="flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $position?->is_active ?? true))>
    <span class="text-sm text-gray-700">{{ __('Aktif') }}</span>
</label>
