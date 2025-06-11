<?php

use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\VehicleType;

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    // Propriedades para filtros
    #[Url(as: 's', history: true, keep: true)]
    public string $searchTerm = ''; // Para pesquisar por placa, marca, modelo, etc.

    #[Url(as: 'brand', history: true, keep: true)]
    public string $filterBrandId = '';

    #[Url(as: 'model', history: true, keep: true)]
    public string $filterVehicleModelId = '';

    #[Url(as: 'type', history: true, keep: true)]
    public string $filterVehicleTypeId = '';

    #[Url(as: 'status', history: true, keep: true)]
    public string $filterStatus = '';

    // Propriedade para itens do cabeçalho da página
    public array $headerItems = [];

    // Propriedades computadas para os dados dos filtros
    #[Computed]
    public function allBrands()
    {
        return VehicleBrand::orderBy('name')->get();
    }

    #[Computed]
    public function allVehicleModelsForFilter()
    {
        return VehicleModel::when($this->filterBrandId, function ($query) {
            $query->where('brand_id', $this->filterBrandId);
        })->orderBy('name')->get();
    }

    #[Computed]
    public function allVehicleTypes()
    {
        return VehicleType::orderBy('name')->get();
    }

    #[Computed]
    public function allStatuses()
    {
        // Retorne os status possíveis do seu modelo Vehicle
        return ['Ativo', 'Em Manutenção', 'Inativo', 'Baixado'];
    }

    /**
     * Reseta a paginação ao alterar qualquer filtro.
     */
    public function updated($property): void
    {
        if (in_array($property, [
            'searchTerm',
            'filterBrandId',
            'filterVehicleModelId',
            'filterVehicleTypeId',
            'filterStatus'
        ])) {
            $this->resetPage();
        }
    }

    /**
     * Método de inicialização do componente.
     */
    public function mount(): void
    {
        // Configura os itens do cabeçalho da página para a listagem geral de veículos
        $this->headerItems = [
            [
                'label' => 'Módulo de Localização | Veículos',
                'description' => 'Visualize e rastreie a localização de todos os veículos da frota.',
                'icon' => 'tabler--map', // Ícone geral de mapa
            ]
        ];
    }

    /**
     * Retorna os dados para a view (todos os veículos).
     */
    public function with(): array
    {
        $vehiclesQuery = Vehicle::query()
            ->with(['vehicleModel.brand', 'vehicleType']) // Eager load para evitar N+1
            ->when($this->searchTerm, function ($query) {
                $query->where('license_plate', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('vehicleModel', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%')
                            ->orWhereHas('brand', function ($r) {
                                $r->where('name', 'like', '%' . $this->searchTerm . '%');
                            });
                    });
            })
            ->when($this->filterBrandId, function ($query) {
                $query->whereHas('vehicleModel.brand', function ($q) {
                    $q->where('id', $this->filterBrandId);
                });
            })
            ->when($this->filterVehicleModelId, function ($query) {
                $query->where('vehicle_model_id', $this->filterVehicleModelId);
            })
            ->when($this->filterVehicleTypeId, function ($query) {
                $query->where('vehicle_type_id', $this->filterVehicleTypeId);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            });

        $vehicles = $vehiclesQuery->orderBy('license_plate')->paginate(10); // Pagina os veículos

        return [
            'vehicles' => $vehicles,
            'headerItems' => $this->headerItems,
        ];
    }
}; ?>

{{-- HTML da View Blade --}}
<div>
    {{-- Componente de Cabeçalho --}}
    <x-header-module :items="$headerItems" />

    {{-- Mensagens de Sucesso/Erro --}}
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
    <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-md p-3 mb-6">
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
                           class="block w-full pl-10 pr-3 py-2 border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md placeholder-neutral-400 dark:placeholder-neutral-500">
                </div>
            </div>
            {{-- Filtro de Marca --}}
            <div>
                <label for="filterBrandId" class="sr-only">Filtrar por Marca</label>
                <div class="relative">
                    <select wire:model.live="filterBrandId" id="filterBrandId"
                            class="block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md py-2 pl-3 pr-8 appearance-none">
                        <option value="">Todas as Marcas</option>
                        @foreach ($this->allBrands as $brand)
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
                            class="block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md py-2 pl-3 pr-8 appearance-none"
                        {{ $this->allVehicleModelsForFilter->isEmpty() && !$filterBrandId ? 'disabled' : '' }}>
                        <option value="">Todos os Modelos</option>
                        @if($filterBrandId && $this->allVehicleModelsForFilter->isNotEmpty())
                            @foreach ($this->allVehicleModelsForFilter as $model)
                                <option value="{{ $model->id }}">{{ $model->name }}</option>
                            @endforeach
                        @elseif(!$filterBrandId && $this->allVehicleModelsForFilter->isEmpty())
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
                            class="block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md py-2 pl-3 pr-8 appearance-none">
                        <option value="">Todos os Tipos</option>
                        @foreach ($this->allVehicleTypes as $type)
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
                            class="block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md py-2 pl-3 pr-8 appearance-none">
                        <option value="">Todos os Status</option>
                        @foreach ($this->allStatuses as $statusValue)
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

    {{-- Tabela de Veículos Simplificada para Localização --}}
    <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-md overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-700">
            <tr>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Placa</th>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Marca / Modelo</th>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Localizar Veículo</th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
            @forelse ($vehicles as $vehicle)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700">
                    <td class="px-4 py-2 whitespace-nowrap">
                        @if($vehicle->license_plate)
                            <div class="inline-flex items-center justify-center h-7 px-2 border border-neutral-400 dark:border-neutral-600 shadow-xs rounded-xs bg-neutral-100 dark:bg-neutral-700 shrink-0">
                                <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-100 tracking-wider">{{ $vehicle->license_plate }}</span>
                            </div>
                        @else
                            <span class="text-xs text-neutral-500 dark:text-neutral-400 italic">Sem Placa</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">
                        {{ $vehicle->vehicleModel->brand->name ?? 'N/D' }} / {{ $vehicle->vehicleModel->name ?? 'N/D' }}
                    </td>
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
                        {{-- Link para a página de rastreamento de UM veículo específico --}}
                        <a href="{{ route('vehicle.track', ['vehicleId' => $vehicle->id]) }}" wire:navigate
                           class="text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 p-1 rounded-md hover:bg-green-100 dark:hover:bg-green-900 transition-colors duration-200 inline-flex items-center"
                           aria-label="Localizar veículo {{ $vehicle->license_plate }}"
                           title="Localizar Veículo">
                            <span class="icon-[tabler--map-pin] text-lg"></span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                        Nenhum veículo encontrado para rastreamento com os filtros aplicados.
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
