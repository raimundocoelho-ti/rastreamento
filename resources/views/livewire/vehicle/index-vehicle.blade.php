<?php

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;

new #[Layout('components.layouts.app')] class extends Component
{
    use WithPagination;

    #[Url(as: 's', history: true, keep: true)]
    public string $searchTerm = '';

    #[Url(as: 'vt', history: true, keep: true)]
    public string $filterVehicleTypeId = '';

    #[Url(as: 'st', history: true, keep: true)]
    public string $filterStatus = '';

    #[Url(as: 'b', history: true, keep: true)]
    public string $filterBrandId = '';

    #[Url(as: 'm', history: true, keep: true)]
    public string $filterVehicleModelId = '';

    public $allVehicleTypes;
    public $allBrands;
    public $allVehicleModelsForFilter;
    public array $headerItems = [];

    public array $allStatuses = ['Ativo', 'Em Manutenção', 'Inativo', 'Baixado'];

    public function mount(): void
    {
        $this->allVehicleTypes = VehicleType::orderBy('name')->get();
        $this->allBrands = VehicleBrand::orderBy('name')->get();
        $this->allVehicleModelsForFilter = collect();

        if ($this->filterBrandId) {
            $this->updatedFilterBrandId($this->filterBrandId);
        }

        $this->headerItems = [
            [
                'label' => 'Veículo | Listagem',
                'description'=> 'Manutenção de Veículos do Sistema',
                'icon' => 'tabler--truck',
                'route' => 'vehicles.create',
                'labelBtn' => 'Novo',
            ]
        ];
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['searchTerm', 'filterVehicleTypeId', 'filterStatus', 'filterBrandId', 'filterVehicleModelId'])) {
            $this->resetPage();
        }
    }

    public function updatedFilterBrandId($brandId): void
    {
        if (!empty($brandId)) {
            $this->allVehicleModelsForFilter = VehicleModel::where('brand_id', $brandId)->orderBy('name')->get();
        } else {
            $this->allVehicleModelsForFilter = collect();
        }
        $this->filterVehicleModelId = '';
        $this->resetPage();
    }

    public function requestVehicleDeletion(int $vehicleId): void
    {
        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle) {
            session()->flash('error', 'Veículo não encontrado.');
            return;
        }

        $this->dispatch(
            'openConfirmationModal',
            title: 'Confirmar Exclusão de Veículo',
            message: 'Tem a certeza que quer apagar o veículo "' . htmlspecialchars($vehicle->license_plate ?: 'ID: ' . $vehicle->id) . '"? Esta ação não pode ser revertida.',
            eventToDispatchOnConfirm: 'deleteVehicleConfirmed',
            eventParams: ['vehicleId' => $vehicleId],
            confirmButtonText: 'Sim, Apagar',
            cancelButtonText: 'Cancelar'
        );
    }

    #[On('deleteVehicleConfirmed')]
    public function deleteVehicleConfirmed(int $vehicleId): void
    {
        $vehicle = Vehicle::find($vehicleId);

        if (!$vehicle) {
            session()->flash('error', 'Veículo não encontrado ou já foi apagado.');
            return;
        }
        $vehicleIdentifier = $vehicle->license_plate ?: 'ID: ' . $vehicle->id;

        if ($vehicle->image) {
            Storage::disk('public')->delete($vehicle->image);
        }

        if ($vehicle->delete()) {
            session()->flash('success', 'Veículo "' . htmlspecialchars($vehicleIdentifier) . '" apagado com sucesso.');
        } else {
            session()->flash('error', 'Ocorreu um erro ao tentar apagar o veículo.');
        }
    }

    public function with(): array
    {
        $vehiclesQuery = Vehicle::with(['vehicleType', 'vehicleModel.brand'])
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($q) {
                    $q->where('license_plate', 'like', '%' . $this->searchTerm . '%')
                        ->orWhereHas('vehicleModel', function ($qModel) {
                            $qModel->where('name', 'like', '%' . $this->searchTerm . '%')
                                ->orWhereHas('brand', function ($qBrand) {
                                    $qBrand->where('name', 'like', '%' . $this->searchTerm . '%');
                                });
                        });
                });
            })
            ->when($this->filterVehicleTypeId, function ($query) {
                $query->where('vehicle_type_id', $this->filterVehicleTypeId);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterBrandId, function ($query) {
                $query->whereHas('vehicleModel', function ($qModel) {
                    $qModel->where('brand_id', $this->filterBrandId);
                });
            })
            ->when($this->filterVehicleModelId, function ($query) {
                $query->where('vehicle_model_id', $this->filterVehicleModelId);
            });

        $vehicles = $vehiclesQuery->orderByRaw("CASE WHEN license_plate IS NULL OR license_plate = '' THEN 1 ELSE 0 END, license_plate ASC")
            ->paginate(10);

        return [
            'vehicles' => $vehicles,
            'headerItems' => $this->headerItems,
        ];
    }
}; ?>
<div>
    {{-- Componente de Cabeçalho (x-header-module) --}}
    {{-- Com título e descrição compactos, e botão de ação alinhado --}}
    <x-header-module :items="$headerItems" />

    {{-- Exibição de Mensagens de Sucesso ou Erro --}}
    {{-- Padronizado com bordas arredondadas para consistência --}}
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

    {{-- Bloco de Filtros de Pesquisa --}}
    {{-- Compacto, com ícones e labels acessíveis, e campos de seleção estilizados --}}
    <div class="bg-white dark:bg-neutral-800 shadow-sm border border-neutral-200 dark:border-neutral-700 rounded-md p-3 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
            {{-- Campo de Pesquisa Geral --}}
            <div class="lg:col-span-1 xl:col-span-1">
                <label for="searchTerm" class="sr-only">Pesquisar por Placa, Marca, Modelo</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="icon-[tabler--search] text-neutral-400 dark:text-neutral-500 text-lg"></span>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="searchTerm" id="searchTerm"
                           placeholder="Placa, Marca, Modelo..."
                           class="block w-full pl-10 pr-3 py-2 border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md placeholder-neutral-400 dark:placeholder-neutral-500">
                </div>
            </div>
            {{-- Filtro de Marca --}}
            <div>
                <label for="filterBrandId" class="sr-only">Filtrar por Marca</label>
                <div class="relative">
                    <select wire:model.live="filterBrandId" id="filterBrandId"
                            class="block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md py-2 pl-3 pr-8 appearance-none">
                        <option value="">Todas as Marcas</option>
                        @foreach ($allBrands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-neutral-700 dark:text-neutral-300">
                        <span class="icon-[tabler--selector] text-lg"></span>
                    </div>
                </div>
            </div>
            {{-- Filtro de Modelo --}}
            <div>
                <label for="filterVehicleModelId" class="sr-only">Filtrar por Modelo</label>
                <div class="relative">
                    <select wire:model.live="filterVehicleModelId" id="filterVehicleModelId"
                            class="block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md py-2 pl-3 pr-8 appearance-none"
                        {{ $allVehicleModelsForFilter->isEmpty() && !$filterBrandId ? 'disabled' : '' }}>
                        <option value="">Todos os Modelos</option>
                        @if($filterBrandId && $allVehicleModelsForFilter->isNotEmpty())
                            @foreach ($allVehicleModelsForFilter as $model)
                                <option value="{{ $model->id }}">{{ $model->name }}</option>
                            @endforeach
                        @elseif(!$filterBrandId && $allVehicleModelsForFilter->isEmpty())
                            <option value="" disabled>Selecione uma marca</option>
                        @endif
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-neutral-700 dark:text-neutral-300">
                        <span class="icon-[tabler--selector] text-lg"></span>
                    </div>
                </div>
            </div>
            {{-- Filtro de Tipo de Veículo --}}
            <div>
                <label for="filterVehicleTypeId" class="sr-only">Filtrar por Tipo de Veículo</label>
                <div class="relative">
                    <select wire:model.live="filterVehicleTypeId" id="filterVehicleTypeId"
                            class="block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md py-2 pl-3 pr-8 appearance-none">
                        <option value="">Todos os Tipos</option>
                        @foreach ($allVehicleTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-neutral-700 dark:text-neutral-300">
                        <span class="icon-[tabler--selector] text-lg"></span>
                    </div>
                </div>
            </div>
            {{-- Filtro de Status --}}
            <div>
                <label for="filterStatus" class="sr-only">Filtrar por Status</label>
                <div class="relative">
                    <select wire:model.live="filterStatus" id="filterStatus"
                            class="block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md py-2 pl-3 pr-8 appearance-none">
                        <option value="">Todos os Status</option>
                        @foreach ($allStatuses as $statusValue)
                            <option value="{{ $statusValue }}">{{ $statusValue }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-neutral-700 dark:text-neutral-300">
                        <span class="icon-[tabler--selector] text-lg"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabela de Listagem --}}
    {{-- Com linhas compactas, ícones de status e ações bem alinhadas --}}
    <div class="bg-white dark:bg-neutral-800 shadow-sm border border-neutral-200 dark:border-neutral-700 rounded-md overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-700">
            <tr>
                {{-- Coluna de Imagem removida --}}
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Placa</th>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Marca / Modelo</th>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Tipo</th>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Cor</th>
                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Nº Lugares</th>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Ações</th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
            @forelse ($vehicles as $vehicle)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700">
                    {{-- Célula de Imagem removida --}}
                    <td class="px-4 py-2 whitespace-nowrap">
                        @if($vehicle->license_plate)
                            {{-- Quadradinho da Placa Padronizado e Compacto --}}
                            <div class="inline-flex items-center justify-center h-7 px-2 border border-neutral-400 dark:border-neutral-600 shadow-sm rounded-sm bg-neutral-100 dark:bg-neutral-700 flex-shrink-0">
                                <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-100 tracking-wider">{{ $vehicle->license_plate }}</span>
                            </div>
                        @else
                            <span class="text-xs text-neutral-500 dark:text-neutral-400 italic">Sem Placa</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">
                        {{ $vehicle->vehicleModel->brand->name ?? 'N/D' }} / {{ $vehicle->vehicleModel->name ?? 'N/D' }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">{{ $vehicle->vehicleType->name ?? 'N/D' }}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">{{ $vehicle->color ?? 'N/D' }}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300 text-center">{{ $vehicle->number_of_seats ?? 'N/D' }}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm">
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($vehicle->status == 'Ativo') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                            @elseif($vehicle->status == 'Em Manutenção') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                            @elseif($vehicle->status == 'Inativo') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                            @elseif($vehicle->status == 'Baixado') bg-neutral-200 text-neutral-800 dark:bg-neutral-600 dark:text-neutral-100
                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                            {{ $vehicle->status }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('vehicles.edit', $vehicle) }}" wire:navigate
                           class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors duration-200 inline-flex items-center"
                           title="Editar">
                            <span class="icon-[tabler--pencil] text-lg"></span>
                        </a>
                        <button wire:click="requestVehicleDeletion({{ $vehicle->id }})" type="button"
                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1 rounded-md hover:bg-red-100 dark:hover:bg-red-900 transition-colors duration-200 inline-flex items-center ml-1"
                                title="Apagar">
                            <span class="icon-[tabler--trash] text-lg"></span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                        Nenhum veículo encontrado.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{-- Paginação --}}
    @if ($vehicles->hasPages())
        <div class="mt-6">
            {{ $vehicles->links() }}
        </div>
    @endif
</div>
