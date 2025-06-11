<?php

use App\Models\Department;
use App\Models\User; // Para listar gestores no filtro
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    #[Url(as: 's', history: true, keep: true)]
    public string $searchName = '';

    #[Url(as: 'm', history: true, keep: true)] // 'm' para manager_id
    public string $filterManagerId = ''; // Usar string para o valor "" do select

    public array $allManagers = [];
    public array $headerItems = [];


    public function mount(): void
    {
        // Carregar apenas utilizadores que podem ser gestores, se houver essa lógica.
        // Por agora, vamos carregar todos para simplificar.
        $this->allManagers = User::orderBy('name')->get(['id', 'name'])->toArray();

        $this->headerItems = [
            [
                'label' => 'Departamentos | Listagem',
                'description'=> 'Manutenção de Departamentos do Sistema',
                'icon' => 'tabler--building-community',
                'route' => 'departments.create',
                'labelBtn' => 'Novo',
            ]
        ];

    }

    public function updatedSearchName(): void
    {
        $this->resetPage();
    }

    public function updatedFilterManagerId(): void
    {
        $this->resetPage();
    }

    public function requestDepartmentDeletion(int $departmentId): void
    {
        $department = Department::find($departmentId);
        if (!$department) {
            session()->flash('error', 'Departamento não encontrado.');
            return;
        }

        $this->dispatch(
            'openConfirmationModal',
            title: 'Confirmar Exclusão de Departamento',
            message: 'Tem a certeza que quer apagar o departamento "' . htmlspecialchars($department->name) . '"? Esta ação não pode ser revertida.',
            eventToDispatchOnConfirm: 'deleteDepartmentConfirmed',
            eventParams: ['departmentId' => $departmentId],
            confirmButtonText: 'Sim, Apagar',
            cancelButtonText: 'Cancelar'
        );
    }

    #[On('deleteDepartmentConfirmed')]
    public function deleteDepartmentConfirmed(int $departmentId): void
    {
        Log::info('deleteDepartmentConfirmed chamado com departmentId: ' . $departmentId);
        $department = Department::find($departmentId); // Usar find em vez de findOrFail para mensagem customizada

        if (!$department) {
            Log::warning('Tentativa de apagar departamento não existente: ' . $departmentId);
            session()->flash('error', 'Departamento não encontrado ou já foi apagado.');
            return;
        }

        $departmentName = $department->name;

        // Apagar imagem associada, se existir
        if ($department->image) {
            Storage::disk('public')->delete($department->image);
            Log::info('Imagem do departamento apagada: ' . $department->image);
        }

        if ($department->delete()) {
            Log::info('Departamento apagado com sucesso: ' . $departmentId . ' Nome: ' . $departmentName);
            session()->flash('success', 'Departamento "' . htmlspecialchars($departmentName) . '" apagado com sucesso.');
        } else {
            Log::error('Falha ao apagar o departamento: ' . $departmentId . ' Nome: ' . $departmentName);
            session()->flash('error', 'Ocorreu um erro ao tentar apagar o departamento.');
        }
    }

    public function with(): array
    {
        $departmentsQuery = Department::with('manager') // Eager load para eficiencia
        ->when($this->searchName, function ($query) {
            $query->where('name', 'like', '%' . $this->searchName . '%');
        })
            ->when($this->filterManagerId, function ($query) {
                $query->where('manager_id', $this->filterManagerId);
            });

        $departments = $departmentsQuery->orderBy('name')->paginate(10);

        return [
            'departments' => $departments,
            'headerItems' => $this->headerItems,
        ];
    }
}; ?>

<div>

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


    <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-md p-3 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <input type="text" wire:model.live.debounce.300ms="searchName" id="searchName"
                       placeholder="Nome do departamento..."
                       class="mt-1 block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 placeholder-neutral-400 dark:placeholder-neutral-500">
            </div>
            <div>
                <select wire:model.live="filterManagerId" id="filterManagerId"
                        class="mt-1 block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 appearance-none">
                    <option value="">Todos os Gestores</option>
                    @foreach ($allManagers as $manager)
                        <option value="{{ $manager['id'] }}">{{ $manager['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div
        class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-none overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-700">
            <tr>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                    Nome
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                    Gestor
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                    Descrição
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                    Ações
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
            @forelse ($departments as $department)
                <tr>
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $department->name }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $department->manager->name ?? 'N/D' }}
                    </td>
                    <td class="px-3 py-2 text-sm text-neutral-500 dark:text-neutral-400">
                        {{ Str::limit($department->description, 50) }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium space-x-3">
                        <a href="{{ route('departments.edit', $department) }}" wire:navigate
                           class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer p-1 inline-flex items-center"
                           aria-label="Editar departamento {{ $department->name }}"
                           title="Editar">
                            <span class="icon-[tabler--pencil] text-xl"></span>
                        </a>

                        <button wire:click="requestDepartmentDeletion({{ $department->id }})"
                                type="button"
                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 cursor-pointer p-1 inline-flex items-center"
                                aria-label="Apagar departamento {{ $department->name }}"
                                title="Apagar">
                            <span class="icon-[tabler--trash] text-xl"></span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5"
                        class="px-3 py-2 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                        Nenhum departamento encontrado.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($departments->hasPages())
        <div class="mt-6">
            {{ $departments->links() }}
        </div>
    @endif
</div>
