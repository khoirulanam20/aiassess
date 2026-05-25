<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-ink dark:text-white">{{ __('Profile') }}</h1>
            <p class="mt-1 text-sm text-ink-muted">{{ __('Manage your account settings') }}</p>
        </div>
    </x-slot>

    <div x-data="{ tab: 'profile' }" class="grid gap-8 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
            <div class="flex gap-1 rounded-xl bg-surface-muted p-1 dark:bg-navy-700" role="tablist" aria-label="{{ __('Profile tabs') }}">
                <button @click="tab = 'profile'" :class="tab === 'profile' ? 'bg-white shadow-sm dark:bg-navy-600' : ''"
                        class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-all text-ink-muted dark:text-gray-400"
                        role="tab" :aria-selected="tab === 'profile'">
                    {{ __('Profile') }}
                </button>
                <button @click="tab = 'security'" :class="tab === 'security' ? 'bg-white shadow-sm dark:bg-navy-600' : ''"
                        class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-all text-ink-muted dark:text-gray-400"
                        role="tab" :aria-selected="tab === 'security'">
                    {{ __('Security') }}
                </button>
                <button @click="tab = 'activity'" :class="tab === 'activity' ? 'bg-white shadow-sm dark:bg-navy-600' : ''"
                        class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-all text-ink-muted dark:text-gray-400"
                        role="tab" :aria-selected="tab === 'activity'">
                    {{ __('Activity') }}
                </button>
            </div>

            <div x-show="tab === 'profile'" x-cloak class="space-y-6">
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Personal Information') }}</h3>
                    <p class="mb-6 text-sm text-ink-muted">{{ __('Update your profile details.') }}</p>
                    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                        @csrf
                        @method('patch')

                        <div>
                            <label class="label-field" for="name">{{ __('Full Name') }}</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="input-field @error('name') border-red-400 @enderror">
                            @error('name') <p class="mt-1.5 text-xs text-red-500 dark:text-red-400" role="alert">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label-field" for="email">{{ __('Email') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="input-field @error('email') border-red-400 @enderror">
                            @error('email') <p class="mt-1.5 text-xs text-red-500 dark:text-red-400" role="alert">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label-field" for="phone">{{ __('Phone') }}</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->userDetail?->phone) }}"
                                       class="input-field">
                            </div>
                            <div>
                                <label class="label-field" for="language">{{ __('Language') }}</label>
                                <select id="language" name="language" class="select-field">
                                    <option value="id" @selected(($user->userDetail?->language ?? 'id') === 'id')>Bahasa Indonesia</option>
                                    <option value="en" @selected(($user->userDetail?->language ?? 'id') === 'en')>English</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label-field" for="company">{{ __('Company') }}</label>
                                <input type="text" id="company" name="company" value="{{ old('company', $user->userDetail?->company) }}"
                                       class="input-field">
                            </div>
                            <div>
                                <label class="label-field" for="position">{{ __('Position') }}</label>
                                <input type="text" id="position" name="position" value="{{ old('position', $user->userDetail?->position) }}"
                                       class="input-field">
                            </div>
                        </div>

                        <div>
                            <label class="label-field" for="bio">{{ __('Bio') }}</label>
                            <textarea id="bio" name="bio" rows="3" class="input-field">{{ old('bio', $user->userDetail?->bio) }}</textarea>
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <x-ui.button type="submit" variant="primary">
                                {{ __('Simpan') }}
                            </x-ui.button>
                            @if (session('status') === 'profile-updated')
                                <span x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show"
                                      class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('Saved!') }}</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="tab === 'security'" x-cloak class="space-y-6">
                <div class="card">
                    @include('profile.partials.update-password-form')
                </div>

                <div class="card border-red-200 dark:border-red-800">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            <div x-show="tab === 'activity'" x-cloak class="space-y-6">
                <div class="card overflow-hidden p-0">
                    @if (empty($activities))
                        <div class="py-12 text-center">
                            <p class="text-sm text-ink-muted">{{ __('No recent activity.') }}</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($activities as $log)
                                <div class="flex items-start gap-3 px-6 py-4">
                                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs"
                                          @class([
                                              'bg-sky-100 text-sky-600 dark:bg-navy-700 dark:text-sky-400' => Str::startsWith($log['action'], 'assessment'),
                                              'bg-green-100 text-green-600 dark:bg-navy-700 dark:text-green-400' => $log['action'] === 'login',
                                              'bg-red-100 text-red-600 dark:bg-navy-700 dark:text-red-400' => $log['action'] === 'logout',
                                              'bg-amber-100 text-amber-600 dark:bg-navy-700 dark:text-amber-400' => $log['action'] === 'profile_update' || $log['action'] === 'avatar_update' || $log['action'] === 'password_change',
                                              'bg-gray-100 text-gray-600 dark:bg-navy-700 dark:text-gray-400' => true,
                                          ])>
                                        @switch($log['action'])
                                            @case('login')
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                                @break
                                            @case('logout')
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                @break
                                            @default
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endswitch
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-ink dark:text-white">{{ __(Str::headline($log['action'])) }}</p>
                                        @if ($log['description'])
                                            <p class="truncate text-xs text-ink-muted">{{ $log['description'] }}</p>
                                        @endif
                                    </div>
                                    <span class="shrink-0 text-xs text-ink-light">{{ \Carbon\Carbon::parse($log['created_at'])->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6 lg:col-span-1">
            <div class="card text-center">
                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" x-data="avatarUpload()">
                    @csrf
                    <div class="relative mx-auto mb-4">
                        <div class="mx-auto flex h-24 w-24 items-center justify-center overflow-hidden rounded-full bg-sky-100 text-3xl font-bold text-sky-600 ring-4 ring-white dark:bg-navy-700 dark:text-sky-400 dark:ring-navy-600"
                             x-ref="avatarPreview">
                            <template x-if="!preview">
                                <span>{{ substr($user->name, 0, 2) }}</span>
                            </template>
                            <template x-if="preview">
                                <img :src="preview" class="h-full w-full object-cover" alt="{{ __('Avatar preview') }}">
                            </template>
                        </div>
                        <label class="absolute bottom-0 right-[calc(50%-3rem)] flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-sky-500 text-white shadow-sm hover:bg-sky-600 transition-colors" aria-label="{{ __('Upload avatar') }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <input type="file" name="avatar" accept="image/*" class="hidden" @change="handleUpload($event)" aria-label="{{ __('Choose avatar image') }}">
                        </label>
                    </div>
                </form>
                <h3 class="text-lg font-semibold text-ink dark:text-white">{{ $user->name }}</h3>
                <p class="text-sm text-ink-muted">{{ $user->email }}</p>
                <div class="mt-3 flex justify-center gap-2">
                    <span class="badge-green">{{ __('Verified') }}</span>
                    @if ($user->userDetail?->language === 'id')
                        <span class="badge-blue">ID</span>
                    @else
                        <span class="badge-blue">EN</span>
                    @endif
                </div>
                <div class="mt-6 space-y-2 text-left text-xs text-ink-light">
                    <p>{{ __('Member since :date', ['date' => $user->created_at->format('M Y')]) }}</p>
                    @if ($user->userDetail?->company)
                        <p>{{ $user->userDetail->company }}</p>
                    @endif
                    @if ($user->userDetail?->position)
                        <p>{{ $user->userDetail->position }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function avatarUpload() {
            return {
                preview: null,
                handleUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.preview = URL.createObjectURL(file);
                    event.target.closest('form').submit();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
