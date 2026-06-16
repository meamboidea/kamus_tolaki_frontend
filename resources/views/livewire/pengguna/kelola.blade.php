<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Livewire\Volt\{computed, usesPagination, state};

usesPagination();

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'role' => 'moderator',
    'showCreateForm' => false,
]);

$daftar = computed(fn () => User::orderBy('name')->paginate(15));

$setPeran = function (int $id, ?string $peran): void {
    $u = User::findOrFail($id);

    // Cegah kunci-diri: admin tak boleh mengubah peran akunnya sendiri.
    if ($u->id === Auth::id()) {
        session()->flash('err', 'Tidak bisa mengubah peran akun sendiri.');

        return;
    }

    $u->role = $peran ? Role::from($peran) : null;
    $u->save();

    $label = $u->role?->label() ?? 'tanpa peran';
    session()->flash('ok', "Peran {$u->name} diubah menjadi {$label}.");
};

$createUser = function (): void {
    $this->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',
        'role' => 'required|in:admin,moderator',
    ]);

    User::create([
        'name' => trim($this->name),
        'email' => trim($this->email),
        'password' => Hash::make($this->password),
        'role' => Role::from($this->role),
        'email_verified_at' => now(),
    ]);

    $this->name = '';
    $this->email = '';
    $this->password = '';
    $this->role = 'moderator';
    $this->showCreateForm = false;

    session()->flash('ok', 'Pengguna baru berhasil ditambahkan.');
};

$toggleForm = function (): void {
    $this->showCreateForm = !$this->showCreateForm;
};

?>

<div class="space-y-6">
    @if (session('ok'))
        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 px-4 py-3.5 text-sm text-emerald-800 dark:text-emerald-300 flex items-center gap-2 shadow-sm">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('ok') }}</span>
        </div>
    @endif
    @if (session('err'))
        <div class="rounded-xl bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 px-4 py-3.5 text-sm text-rose-800 dark:text-rose-300 flex items-center gap-2 shadow-sm">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">{{ session('err') }}</span>
        </div>
    @endif

    <!-- Add User Action Trigger -->
    <div class="flex justify-end">
        <button wire:click="toggleForm" type="button" 
            class="inline-flex items-center gap-1.5 rounded-lg bg-brand-700 px-3 py-2 text-xs font-semibold text-white hover:bg-brand-800 dark:bg-brand-600 dark:hover:bg-brand-700 shadow-sm transition-all duration-150 cursor-pointer">
            @if ($showCreateForm)
                Batal
            @else
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pengguna
            @endif
        </button>
    </div>

    <!-- Collapsible Create User Form -->
    @if ($showCreateForm)
        <div class="rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-sm border border-brand-100/30 dark:border-slate-800/80 animate-in fade-in slide-in-from-top-4 duration-200">
            <h3 class="font-serif text-lg font-bold text-slate-900 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-800/50 pb-2">Tambah Pengguna Baru</h3>
            <form wire:submit="createUser" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-450 dark:text-slate-500 uppercase tracking-wide">Nama Lengkap</label>
                        <input type="text" wire:model="name" placeholder="Masukkan nama staf..." required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 focus:outline-none transition-all duration-150 text-slate-900 dark:text-white text-sm">
                        @error('name') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-450 dark:text-slate-500 uppercase tracking-wide">Alamat Email</label>
                        <input type="email" wire:model="email" placeholder="Email untuk login..." required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 focus:outline-none transition-all duration-150 text-slate-900 dark:text-white text-sm">
                        @error('email') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-450 dark:text-slate-500 uppercase tracking-wide">Kata Sandi (Min. 8 Karakter)</label>
                        <input type="password" wire:model="password" placeholder="Masukkan kata sandi..." required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 focus:outline-none transition-all duration-150 text-slate-900 dark:text-white text-sm">
                        @error('password') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Role -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-450 dark:text-slate-500 uppercase tracking-wide">Peran Pengguna</label>
                        <select wire:model="role" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 focus:border-brand-500 focus:ring-brand-500 focus:ring-2 focus:outline-none transition-all duration-150 text-slate-900 dark:text-white text-sm">
                            <option value="moderator">Moderator</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 dark:bg-brand-600 dark:hover:bg-brand-700 shadow-md shadow-brand-700/10 dark:shadow-none transition-all duration-150 cursor-pointer">
                        Simpan Pengguna
                    </button>
                    <button wire:click="toggleForm" type="button"
                        class="rounded-xl border border-slate-200 dark:border-slate-800 px-5 py-2.5 text-sm font-semibold text-slate-650 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-850 transition-all duration-150 cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Users List Table Container -->
    <div class="overflow-hidden rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-brand-100/30 dark:border-slate-800/80">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800/50">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Nama</th>
                        <th class="px-5 py-4 font-semibold">Email</th>
                        <th class="px-5 py-4 font-semibold">Peran</th>
                        <th class="px-5 py-4 font-semibold text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @foreach ($this->daftar as $u)
                        <tr wire:key="u-{{ $u->id }}" class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20 transition-colors duration-150">
                            <td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">
                                {{ $u->name }}
                                @if ($u->id === auth()->id())
                                    <span class="ml-1 text-xs text-slate-400 font-normal">(Anda)</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $u->email }}</td>
                            <td class="px-5 py-4">
                                @php($r = $u->role)
                                <span @class([
                                    'rounded-full px-2.5 py-0.5 text-2xs font-semibold uppercase tracking-wider border',
                                    'bg-purple-50 text-purple-700 border-purple-150 dark:bg-purple-950/40 dark:text-purple-300 dark:border-none' => $r === \App\Enums\Role::Admin,
                                    'bg-blue-50 text-blue-700 border-blue-150 dark:bg-blue-950/40 dark:text-blue-300 dark:border-none' => $r === \App\Enums\Role::Moderator,
                                    'bg-slate-150 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-none' => $r === null,
                                ])>
                                    {{ $r?->label() ?? 'Tanpa peran' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if ($u->id === auth()->id())
                                    <span class="block text-right text-xs text-slate-400">—</span>
                                @else
                                    <div class="flex flex-wrap justify-end gap-2">
                                        @if ($r !== \App\Enums\Role::Moderator)
                                            <button wire:click="setPeran({{ $u->id }}, 'moderator')"
                                                class="rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-none px-3 py-1.5 text-xs font-semibold text-blue-700 dark:text-blue-350 hover:bg-blue-100/50 transition-colors duration-150 cursor-pointer">
                                                Jadikan Moderator
                                            </button>
                                        @endif
                                        @if ($r !== \App\Enums\Role::Admin)
                                            <button wire:click="setPeran({{ $u->id }}, 'admin')"
                                                wire:confirm="Jadikan {{ $u->name }} sebagai Admin?"
                                                class="rounded-lg bg-purple-50 dark:bg-purple-950/40 border border-purple-100 dark:border-none px-3 py-1.5 text-xs font-semibold text-purple-700 dark:text-purple-350 hover:bg-purple-100/50 transition-colors duration-150 cursor-pointer">
                                                Jadikan Admin
                                            </button>
                                        @endif
                                        @if ($r !== null)
                                            <button wire:click="setPeran({{ $u->id }}, null)"
                                                wire:confirm="Cabut peran {{ $u->name }}?"
                                                class="rounded-lg bg-slate-100 dark:bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-200 transition-colors duration-150 cursor-pointer">
                                                Cabut
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $this->daftar->links() }}</div>
</div>
