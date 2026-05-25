@props(['departments', 'positions', 'candidate' => null])

@php
    $positionOptions = $positions->map(fn ($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'department_id' => $p->department_id,
    ])->values();
@endphp

<div
    x-data="candidateMasterData({
        positions: @js($positionOptions),
        departmentId: @js((string) old('department_id', $candidate?->userDetail?->department_id ?? '')),
        positionId: @js((string) old('position_id', $candidate?->userDetail?->position_id ?? '')),
    })"
    class="space-y-4"
>
    <div>
        <x-input-label for="department_id" :value="__('Departemen')" />
        <select
            id="department_id"
            name="department_id"
            x-model="departmentId"
            @change="onDepartmentChange()"
            class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
        >
            <option value="">{{ __('— Pilih departemen —') }}</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('department_id')" class="mt-1" />
        @if ($departments->isEmpty())
            <p class="mt-1 text-xs text-amber-600">
                {{ __('Belum ada departemen.') }}
                <a href="{{ route('admin.master-data.departments.create') }}" class="text-sky-600 hover:underline">{{ __('Tambah departemen') }}</a>
            </p>
        @endif
    </div>

    <div>
        <x-input-label for="position_id" :value="__('Posisi')" />
        <select
            id="position_id"
            name="position_id"
            x-model="positionId"
            :disabled="!departmentId"
            class="mt-1 block w-full rounded-lg border-gray-300 text-sm disabled:bg-gray-100 disabled:text-gray-400"
        >
            <option value="" x-text="departmentId ? '{{ __('— Pilih posisi —') }}' : '{{ __('Pilih departemen terlebih dahulu') }}'"></option>
            <template x-for="position in filteredPositions" :key="position.id">
                <option :value="position.id" x-text="position.name"></option>
            </template>
        </select>
        <p class="mt-1 text-xs text-gray-500" x-show="departmentId && filteredPositions.length === 0" x-cloak>
            {{ __('Belum ada posisi untuk departemen ini.') }}
            <a href="{{ route('admin.master-data.positions.create') }}" class="text-sky-600 hover:underline">{{ __('Tambah posisi') }}</a>
        </p>
        <x-input-error :messages="$errors->get('position_id')" class="mt-1" />
        @if ($positions->isEmpty())
            <p class="mt-1 text-xs text-amber-600">
                {{ __('Belum ada posisi.') }}
                <a href="{{ route('admin.master-data.positions.create') }}" class="text-sky-600 hover:underline">{{ __('Tambah posisi') }}</a>
            </p>
        @endif
    </div>
</div>

@once
    @push('scripts')
        <script>
            function candidateMasterData({ positions, departmentId, positionId }) {
                return {
                    departmentId: departmentId ?? '',
                    positionId: positionId ?? '',
                    allPositions: positions ?? [],
                    get filteredPositions() {
                        if (!this.departmentId) {
                            return [];
                        }

                        return this.allPositions.filter(
                            (p) => String(p.department_id) === String(this.departmentId)
                        );
                    },
                    onDepartmentChange() {
                        const valid = this.filteredPositions.some(
                            (p) => String(p.id) === String(this.positionId)
                        );
                        if (!valid) {
                            this.positionId = '';
                        }
                    },
                };
            }
        </script>
    @endpush
@endonce
