@props([
    'namePrefix' => 'questions',
    'questions' => [],
    'showDimension' => false,
    'groupKey' => null,
])

@php
    $prefix = $groupKey !== null ? "{$namePrefix}[{$groupKey}][questions]" : $namePrefix;
@endphp

<div
    class="space-y-4"
    x-data="questionEditor(@js($questions), @js($prefix), @js($showDimension))"
>
    <template x-for="(question, qIndex) in items" :key="qIndex">
        <details class="rounded-lg border border-gray-200 bg-white" open>
            <summary class="flex cursor-pointer list-none items-center justify-between gap-2 px-4 py-3 font-medium text-ink">
                <span x-text="`Soal ${qIndex + 1}: ${question.code || '(belum ada kode)'}`"></span>
                <x-ui.icon-action
                    type="button"
                    variant="danger"
                    :title="__('Hapus soal')"
                    @click.prevent="removeQuestion(qIndex)"
                >
                    <x-ui.icon name="trash" />
                </x-ui.icon-action>
            </summary>
            <div class="space-y-3 border-t border-gray-100 px-4 py-4">
                <div class="grid gap-3 sm:grid-cols-4">
                    <div class="sm:col-span-1">
                        <label class="text-sm font-medium text-gray-700">{{ __('Kode') }}</label>
                        <input
                            type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                            x-model="question.code"
                            :name="`${prefix}[${qIndex}][code]`"
                        >
                    </div>
                    <div class="sm:col-span-3">
                        <label class="text-sm font-medium text-gray-700">{{ __('Pertanyaan') }}</label>
                        <textarea
                            rows="2"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                            x-model="question.question"
                            :name="`${prefix}[${qIndex}][question]`"
                        ></textarea>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">{{ __('Opsi jawaban') }}</span>
                        <x-ui.icon-action
                            type="button"
                            variant="primary"
                            :title="__('Tambah opsi')"
                            @click.prevent="addOption(qIndex)"
                        >
                            <x-ui.icon name="plus" />
                        </x-ui.icon-action>
                    </div>

                    <template x-for="(option, oIndex) in question.options" :key="oIndex">
                        <div class="grid gap-2 rounded-md bg-gray-50 p-3 sm:grid-cols-12">
                            <div class="sm:col-span-1">
                                <label class="text-xs text-gray-500">ID</label>
                                <input
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                                    x-model="option.id"
                                    :name="`${prefix}[${qIndex}][options][${oIndex}][id]`"
                                >
                            </div>
                            <div :class="showDimension ? 'sm:col-span-8' : 'sm:col-span-10'">
                                <label class="text-xs text-gray-500">{{ __('Teks opsi') }}</label>
                                <input
                                    type="text"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                                    x-model="option.text"
                                    :name="`${prefix}[${qIndex}][options][${oIndex}][text]`"
                                >
                            </div>
                            <template x-if="showDimension">
                                <div class="sm:col-span-2">
                                    <label class="text-xs text-gray-500">{{ __('Dimensi') }}</label>
                                    <input
                                        type="text"
                                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                                        x-model="option.dimension"
                                        :name="`${prefix}[${qIndex}][options][${oIndex}][dimension]`"
                                    >
                                </div>
                            </template>
                            <div class="flex items-end sm:col-span-1">
                                <x-ui.icon-action
                                    type="button"
                                    variant="danger"
                                    :title="__('Hapus opsi')"
                                    @click.prevent="removeOption(qIndex, oIndex)"
                                    x-show="question.options.length > 2"
                                >
                                    <x-ui.icon name="trash" class="h-4 w-4" />
                                </x-ui.icon-action>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </details>
    </template>

    <x-ui.icon-action
        type="button"
        variant="primary"
        :title="__('Tambah soal')"
        class="border border-dashed border-gray-300"
        @click.prevent="addQuestion()"
    >
        <x-ui.icon name="plus" />
    </x-ui.icon-action>
</div>

@once
    @push('scripts')
        <script>
            function questionEditor(initial, prefix, showDimension) {
                const defaultOptions = () => [
                    { id: 'A', text: '', dimension: '' },
                    { id: 'B', text: '', dimension: '' },
                ];

                return {
                    prefix,
                    showDimension,
                    items: (initial && initial.length ? initial : []).map((q) => ({
                        code: q.code ?? '',
                        question: q.question ?? '',
                        options: (q.options ?? defaultOptions()).map((o) => ({
                            id: o.id ?? '',
                            text: o.text ?? '',
                            dimension: o.dimension ?? '',
                        })),
                    })),
                    addQuestion() {
                        const next = this.items.length + 1;
                        this.items.push({
                            code: `Q${String(next).padStart(2, '0')}`,
                            question: '',
                            options: defaultOptions(),
                        });
                    },
                    removeQuestion(index) {
                        this.items.splice(index, 1);
                    },
                    addOption(qIndex) {
                        const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        const n = this.items[qIndex].options.length;
                        this.items[qIndex].options.push({
                            id: letters[n] ?? String(n + 1),
                            text: '',
                            dimension: '',
                        });
                    },
                    removeOption(qIndex, oIndex) {
                        if (this.items[qIndex].options.length <= 2) return;
                        this.items[qIndex].options.splice(oIndex, 1);
                    },
                };
            }
        </script>
    @endpush
@endonce
