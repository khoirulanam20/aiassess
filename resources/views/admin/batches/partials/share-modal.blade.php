@props([
    'open' => false,
    'candidateName' => '',
    'shareUrl' => '',
    'accessCode' => '',
])

<div
    x-data="{
        open: @js($open),
        copiedLink: false,
        copiedCode: false,
        copyLink() {
            navigator.clipboard.writeText(@js($shareUrl));
            this.copiedLink = true;
            setTimeout(() => this.copiedLink = false, 2000);
        },
        copyCode() {
            navigator.clipboard.writeText(@js($accessCode));
            this.copiedCode = true;
            setTimeout(() => this.copiedCode = false, 2000);
        },
    }"
    x-cloak
>
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-navy-500/50 p-4"
        @keydown.escape.window="open = false"
    >
        <div
            x-show="open"
            x-transition
            @click.outside="open = false"
            class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="batch-share-modal-title"
        >
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 id="batch-share-modal-title" class="text-lg font-semibold text-ink">
                        {{ __('Share ke kandidat') }}
                    </h3>
                    @if ($candidateName)
                        <p class="mt-1 text-sm text-gray-500">{{ $candidateName }}</p>
                    @endif
                </div>
                <button type="button" @click="open = false" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600" :title="__('Tutup')">
                    <x-ui.icon name="x" />
                </button>
            </div>

            <p class="mt-4 text-sm text-gray-600">
                {{ __('Kirim link dan kode akses secara terpisah. Link sama untuk semua kandidat; kode unik per orang.') }}
            </p>

            <div class="mt-6 space-y-5">
                <div>
                    <label class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Link assessment') }}</label>
                    <div class="mt-2 flex gap-2">
                        <input
                            type="text"
                            readonly
                            value="{{ $shareUrl }}"
                            class="flex-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm"
                        >
                        <x-ui.icon-action type="button" @click="copyLink()" variant="primary" :title="__('Salin link')" class="shrink-0">
                            <x-ui.icon name="clipboard" />
                        </x-ui.icon-action>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Kode akses (6 digit)') }}</label>
                    <div class="mt-2 flex items-center gap-3">
                        <p class="font-mono text-3xl font-bold tracking-[0.35em] text-sky-700">{{ $accessCode }}</p>
                        <x-ui.icon-action type="button" @click="copyCode()" variant="solid" :title="__('Salin kode')" class="shrink-0">
                            <x-ui.icon name="clipboard" />
                        </x-ui.icon-action>
                    </div>
                    <p class="mt-2 text-xs text-amber-600">{{ __('Kode hanya ditampilkan sekali. Simpan sebelum menutup.') }}</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-ui.button-icon type="button" @click="open = false" variant="solid" icon="check">
                    {{ __('Selesai') }}
                </x-ui.button-icon>
            </div>
        </div>
    </div>
</div>
