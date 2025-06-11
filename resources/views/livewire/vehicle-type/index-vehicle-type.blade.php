<?php

use App\Models\VehicleType;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;

new #[Layout('components.layouts.app')] class extends Component
{
    use WithPagination;

    #[Url(as: 's', history: true, keep: true)]
    public string $searchName = '';
    public array $headerItems = [];

    public function updatedSearchName(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->headerItems = [
            [
                'label' => 'Tipos de Veículos | Listagem',
                'description'=> 'Manutenção de Tipos de Veículos do Sistema',
                'icon' => 'tabler--category-2',
                'route' => 'vehicle-types.create',
                'labelBtn' => 'Novo',
            ]
        ];
    }

    public function requestVehicleTypeDeletion(int $vehicleTypeId): void
    {
        $vehicleType = VehicleType::find($vehicleTypeId);
        if (!$vehicleType) {
            session()->flash('error', 'Tipo de Veículo não encontrado.');
            return;
        }

        $this->dispatch(
            'openConfirmationModal',
            title: 'Confirmar Exclusão de Tipo de Veículo',
            message: 'Tem a certeza que quer apagar o tipo de veículo "' . htmlspecialchars($vehicleType->name) . '"? Esta ação não pode ser revertida.',
            eventToDispatchOnConfirm: 'deleteVehicleTypeConfirmed',
            eventParams: ['vehicleTypeId' => $vehicleTypeId],
            confirmButtonText: 'Sim, Apagar',
            cancelButtonText: 'Cancelar'
        );
    }

    #[On('deleteVehicleTypeConfirmed')]
    public function deleteVehicleTypeConfirmed(int $vehicleTypeId): void
    {
        Log::info('deleteVehicleTypeConfirmed chamado com vehicleTypeId: ' . $vehicleTypeId);
        $vehicleType = VehicleType::find($vehicleTypeId);

        if (!$vehicleType) {
            Log::warning('Tentativa de apagar tipo de veículo não existente: ' . $vehicleTypeId);
            session()->flash('error', 'Tipo de Veículo não encontrado ou já foi apagado.');
            return;
        }

        $vehicleTypeName = $vehicleType->name;

        if ($vehicleType->delete()) {
            Log::info('Tipo de Veículo apagado com sucesso: ' . $vehicleTypeId . ' Nome: ' . $vehicleTypeName);
            session()->flash('success', 'Tipo de Veículo "' . htmlspecialchars($vehicleTypeName) . '" apagado com sucesso.');
        } else {
            Log::error('Falha ao apagar o tipo de veículo: ' . $vehicleTypeId . ' Nome: ' . $vehicleTypeName);
            session()->flash('error', 'Ocorreu um erro ao tentar apagar o tipo de veículo.');
        }
    }

    public function with(): array
    {
        $vehicleTypesQuery = VehicleType::query()
            ->when($this->searchName, function ($query) {
                $query->where('name', 'like', '%' . $this->searchName . '%');
            });

        $vehicleTypes = $vehicleTypesQuery->orderBy('name')->paginate(10);

        return [
            'vehicleTypes' => $vehicleTypes,
            'headerItems' => $this->headerItems,
        ];
    }
}; ?>

<div>

    <x-header-module :items="$headerItems" />

    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded-md relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded-md relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-neutral-800 shadow-sm border border-neutral-200 dark:border-neutral-700 rounded-md p-3 mb-6">
        <label for="searchName" class="sr-only">Pesquisar por Nome</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="icon-[tabler--search] text-neutral-400 dark:text-neutral-500 text-lg"></span>
            </div>
            <input type="text" wire:model.live.debounce.300ms="searchName" id="searchName"
                   placeholder="Pesquisar por nome de veículo..."
                   class="block w-full pl-10 pr-3 py-2 border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md placeholder-neutral-400 dark:placeholder-neutral-500">
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 shadow-sm border border-neutral-200 dark:border-neutral-700 rounded-md overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-700">
            <tr>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Nome</th>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Descrição</th>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Req. Matrícula</th>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Odómetro</th>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Horímetro</th>
                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Ações</th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
            @forelse ($vehicleTypes as $vehicleType)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700">
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $vehicleType->name }}</td>
                    <td class="px-4 py-2 text-sm text-neutral-500 dark:text-neutral-400 max-w-xs truncate" title="{{ $vehicleType->description }}">
                        {{ Str::limit($vehicleType->description, 50) }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                        @if($vehicleType->requires_license_plate)
                            <span class="icon-[tabler--check] text-green-500 text-xl"></span>
                        @else
                            <span class="icon-[tabler--x] text-red-500 text-xl"></span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                        @if($vehicleType->controlled_by_odometer)
                            <span class="icon-[tabler--check] text-green-500 text-xl"></span>
                        @else
                            <span class="icon-[tabler--x] text-red-500 text-xl"></span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                        @if($vehicleType->controlled_by_hour_meter)
                            <span class="icon-[tabler--check] text-green-500 text-xl"></span>
                        @else
                            <span class="icon-[tabler--x] text-red-500 text-xl"></span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('vehicle-types.edit', $vehicleType) }}" wire:navigate
                           class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors duration-200 inline-flex items-center"
                           aria-label="Editar tipo de veículo {{ $vehicleType->name }}"
                           title="Editar">
                            <span class="icon-[tabler--pencil] text-lg"></span>
                        </a>
                        <button wire:click="requestVehicleTypeDeletion({{ $vehicleType->id }})" type="button"
                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1 rounded-md hover:bg-red-100 dark:hover:bg-red-900 transition-colors duration-200 inline-flex items-center ml-1"
                                aria-label="Apagar tipo de veículo {{ $vehicleType->name }}"
                                title="Apagar">
                            <span class="icon-[tabler--trash] text-lg"></span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                        Nenhum tipo de veículo encontrado.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($vehicleTypes->hasPages())
        <div class="mt-6">
            {{ $vehicleTypes->links() }}
        </div>
    @endif
</div>
