<?php

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    #[Url(as: 's', history: true, keep: true)]
    public string $searchName = '';

    #[Url(as: 'r', history: true, keep: true)]
    public string $filterRole = '';

    public array $allRoles = [];
    public array $headerItems = [];

    public function mount(): void
    {
        $this->allRoles = collect(UserRole::cases())->mapWithKeys(function ($role) {
            return [$role->value => Str::title($role->value)];
        })->all();

        $this->headerItems = [
            [
                'label' => 'Usuários | Listagem',
                'description'=> 'Manutenção de Usuários do Sistema',
                'icon' => 'tabler--users',
                'route' => 'users.create',
                'labelBtn' => 'Novo',
            ]
        ];
    }

    public function updatedSearchName(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    public function requestUserDeletion(int $userId): void
    {
        $user = User::find($userId);
        if (!$user) return;

        $this->dispatch(
            'openConfirmationModal',
            title: 'Confirmar Exclusão do Usuário',
            message: 'Tem a certeza que quer apagar o Usuário "' . htmlspecialchars($user->name) . '"? Esta ação não pode ser revertida.',
            eventToDispatchOnConfirm: 'deleteUserConfirmed',
            eventParams: ['userId' => $userId],
            confirmButtonText: 'Sim, Apagar',
            cancelButtonText: 'Cancelar'
        );
    }

    #[On('deleteUserConfirmed')]
    public function deleteUserConfirmed(int $userId): void
    {
        Log::info('deleteUserConfirmed chamado com userId: ' . $userId);
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            Log::warning('Tentativa de apagar o próprio Usuário: ' . $userId);
            session()->flash('error', 'Não pode apagar o seu próprio Usuário.');
            return;
        }

        if ($user->isAdmin() && User::where('role', UserRole::ADMIN)->count() === 1) {
            Log::warning('Tentativa de apagar o único administrador: ' . $userId);
            session()->flash('error', 'Não pode apagar o único administrador.');
            return;
        }

        $userName = $user->name;
        if ($user->delete()) {
            Log::info('Usuário apagado com sucesso: ' . $userId . ' Nome: ' . $userName);
            session()->flash('success', 'Usuário "' . htmlspecialchars($userName) . '" apagado com sucesso.');
        } else {
            Log::error('Falha ao apagar o Usuário: ' . $userId . ' Nome: ' . $userName);
            session()->flash('error', 'Ocorreu um erro ao tentar apagar o Usuário.');
        }
    }

    public function with(): array
    {
        $users = User::query()
            ->when($this->searchName, fn($query) => $query->where('name', 'like', '%' . $this->searchName . '%'))
            ->when($this->filterRole, fn($query) => $query->where('role', $this->filterRole))
            ->orderBy('name')
            ->paginate(10);

        return [
            'users' => $users,
            'headerItems' => $this->headerItems,
            'allRoles' => $this->allRoles,
        ];
    }

};?>

<div>
    <section class="w-full">

        <x-header-module :items="$headerItems" />




        @if (session()->has('success'))
            <div
                class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded-none relative mb-4"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div
                class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded-none relative mb-4"
                role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div
            class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-none p-4 sm:p-6 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="searchName" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Pesquisar
                        por Nome</label>
                    <input type="text" wire:model.live.debounce.300ms="searchName" id="searchName"
                           placeholder="Nome do Usuário..."
                           class="mt-1 block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 placeholder-neutral-400 dark:placeholder-neutral-500">
                </div>
                <div>
                    <label for="filterRole" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Filtrar
                        por Papel</label>
                    <select wire:model.live="filterRole" id="filterRole"
                            class="mt-1 block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 appearance-none">
                        <option value="">Todos os Papéis</option>
                        @foreach ($allRoles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-none overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-200 dark:bg-neutral-700">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                        Nome
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                        Papel
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">{{ Str::title($user->role->value) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                            <a href="{{ route('users.edit', $user) }}" wire:navigate
                               class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer p-1 inline-flex items-center"
                               aria-label="Editar Usuário {{ $user->name }}"
                               title="Editar">
                                <span class="icon-[tabler--pencil] text-xl"></span>
                            </a>

                            <button wire:click="requestUserDeletion({{ $user->id }})"
                                    type="button"
                                    class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 cursor-pointer p-1 inline-flex items-center"
                                    aria-label="Apagar Usuário {{ $user->name }}"
                                    title="Apagar">
                                <span class="icon-[tabler--trash] text-xl"></span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                            Nenhum Usuário encontrado.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        @endif
    </section>
</div>
