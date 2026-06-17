<?php

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use function Livewire\Volt\{state, usesFileUploads};

usesFileUploads();

state([
    'name' => fn () => auth()->user()->name,
    'email' => fn () => auth()->user()->email,
    'foto' => null,
]);

$updateProfileInformation = function () {
    $user = Auth::user();

    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        'foto' => ['nullable', 'image', 'max:2048'], // maks 2 MB
    ]);

    $user->fill(['name' => $validated['name'], 'email' => $validated['email']]);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    if ($this->foto) {
        // Hapus foto lama agar tidak menumpuk, lalu simpan yang baru.
        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }
        $user->foto_profil = $this->foto->store('foto-profil', 'public');
    }

    $user->save();

    $this->foto = null;

    $this->dispatch('profile-updated', name: $user->name);
};

$hapusFoto = function () {
    $user = Auth::user();

    if ($user->foto_profil) {
        Storage::disk('public')->delete($user->foto_profil);
        $user->update(['foto_profil' => null]);
    }

    $this->foto = null;
    $this->dispatch('profile-updated', name: $user->name);
};

$sendVerification = function () {
    $user = Auth::user();

    if ($user->hasVerifiedEmail()) {
        $this->redirectIntended(default: route('dashboard', absolute: false));

        return;
    }

    $user->sendEmailVerificationNotification();

    Session::flash('status', 'verification-link-sent');
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        {{-- Foto profil --}}
        <div>
            <x-input-label :value="__('Foto Profil')" />
            <div class="mt-2 flex items-center gap-4">
                @php $user = auth()->user(); @endphp
                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-full bg-brand-100 dark:bg-brand-900/40 ring-1 ring-gray-200 dark:ring-gray-700">
                    @if ($foto)
                        <img src="{{ $foto->temporaryUrl() }}" alt="Pratinjau" class="h-full w-full object-cover">
                    @elseif ($user->foto_url)
                        <img src="{{ $user->foto_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                    @else
                        <span class="flex h-full w-full items-center justify-center text-2xl font-bold text-brand-700 dark:text-brand-300">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    @endif
                </div>
                <div class="space-y-2">
                    <input type="file" wire:model="foto" accept="image/*"
                        class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 dark:file:bg-brand-900/30 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-brand-700 dark:file:text-brand-300 hover:file:bg-brand-100">
                    <div wire:loading wire:target="foto" class="text-xs text-gray-500">Mengunggah…</div>
                    @if ($user->foto_profil)
                        <button type="button" wire:click="hapusFoto" wire:confirm="Hapus foto profil?"
                            class="text-xs text-red-600 dark:text-red-400 hover:underline">
                            Hapus foto
                        </button>
                    @endif
                </div>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPG/PNG, maksimal 2 MB.</p>
            <x-input-error class="mt-2" :messages="$errors->get('foto')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
