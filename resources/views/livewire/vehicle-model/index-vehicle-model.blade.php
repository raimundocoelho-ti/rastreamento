<?php

use App\Models\VehicleModel; // Certifique-se que este é o Model correto
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

    public function mount(): void
    {
        $this->headerItems = [
            [
                'label' => 'Modelos de Veículos | Listagem',
                'description'=> 'Manutenção de Modelos de veículo do Sistema',
                'icon' => 'tabler--assembly',
                'route' => 'vehicle-models.create',
                'labelBtn' => 'Novo',
            ]
        ];

    }

    public function updatedSearchName(): void
    {
        $this->resetPage();
    }

    public function requestVehicleModelDeletion(int $vehicleModelId): void
    {
        $vehicleModel = VehicleModel::find($vehicleModelId);
        if (!$vehicleModel) {
            session()->flash('error', 'Modelo não encontrado.'); // Mensagem ajustada
            return;
        }

        // Verifica se o modelo está em uso por algum veículo (Assumindo que VehicleModel tem um relacionamento vehicles())
        // Se VehicleModel não tiver essa relação ainda, você pode comentar ou remover este bloco IF.
        if (method_exists($vehicleModel, 'vehicles') && $vehicleModel->vehicles()->exists()) {
            session()->flash('error', 'O modelo "' . htmlspecialchars($vehicleModel->name) . '" está em uso por um ou mais veículos e não pode ser apagado.'); // Mensagem ajustada
            return;
        }

        $this->dispatch(
            'openConfirmationModal',
            title: 'Confirmar Exclusão de Modelo', // Título ajustado
            message: 'Tem a certeza que quer apagar o modelo "' . htmlspecialchars($vehicleModel->name) . '"? Esta ação não pode ser revertida.', // Mensagem ajustada
            eventToDispatchOnConfirm: 'deleteVehicleModelConfirmed',
            eventParams: ['vehicleModelId' => $vehicleModelId],
            confirmButtonText: 'Sim, Apagar',
            cancelButtonText: 'Cancelar'
        );
    }

    #[On('deleteVehicleModelConfirmed')]
    public function deleteVehicleModelConfirmed(int $vehicleModelId): void
    {
        $vehicleModel = VehicleModel::find($vehicleModelId);

        if (!$vehicleModel) {
            session()->flash('error', 'Modelo não encontrado ou já foi apagado.'); // Mensagem ajustada
            return;
        }

        // Re-verificar dependências antes de apagar
        if (method_exists($vehicleModel, 'vehicles') && $vehicleModel->vehicles()->exists()) {
            session()->flash('error', 'O modelo "' . htmlspecialchars($vehicleModel->name) . '" está em uso e não pode ser apagado. Verifique os veículos associados.'); // Mensagem ajustada
            return;
        }

        $vehicleModelName = $vehicleModel->name;
        if ($vehicleModel->delete()) {
            session()->flash('success', 'Modelo "' . htmlspecialchars($vehicleModelName) . '" apagado com sucesso.'); // Mensagem ajustada
        } else {
            session()->flash('error', 'Ocorreu um erro ao tentar apagar o modelo.'); // Mensagem ajustada
        }
    }

    public function with(): array
    {
        $vehicleModelsQuery = VehicleModel::query()
            ->when($this->searchName, function ($query) {
                $query->where('name', 'like', '%' . $this->searchName . '%');
            });

        $vehicleModels = $vehicleModelsQuery->orderBy('name')->paginate(10);

        return [
            'vehicleModels' => $vehicleModels, // Chave do array ajustada para camelCase
            'headerItems' => $this->headerItems,
        ];
    }
}; ?>

<div>
    {{-- Componente de Cabeçalho (x-header-module) --}}
    {{-- Com título e descrição compactos, e botão de ação alinhado --}}
    <x-header-module :items="$headerItems" />

    {{-- Exibição de Mensagens de Sucesso ou Erro --}}
    {{-- Mantido o estilo de alerta padrão para feedback visual --}}
    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-rose-200 px-4 py-3 rounded-md relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded-md relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Bloco de Pesquisa --}}
    {{-- Compacto, com campo de input, ícone de lupa e label oculta para acessibilidade --}}
    <div class="bg-white dark:bg-neutral-800 shadow-sm border border-neutral-200 dark:border-neutral-700 rounded-md p-3 mb-6">
        <label for="searchName" class="sr-only">Pesquisar por Nome</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="icon-[tabler--search] text-neutral-400 dark:text-neutral-500 text-lg"></span>
            </div>
            <input type="text" wire:model.live.debounce.300ms="searchName" id="searchName"
                   placeholder="Pesquisar por nome do modelo..."
                   class="block w-full pl-10 pr-3 py-2 border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md placeholder-neutral-400 dark:placeholder-neutral-500">
        </div>
    </div>

    {{-- Tabela de Listagem --}}
    {{-- Com linhas compactas, ícones de status e ações bem alinhadas --}}
    <div class="bg-white dark:bg-neutral-800 shadow-sm border border-neutral-200 dark:border-neutral-700 rounded-md overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-700">
            <tr>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                    Nome do Modelo
                </th>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                    Data de Criação
                </th>
                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                    Ações
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
            @forelse ($vehicleModels as $vehicleModel)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700">
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $vehicleModel->name }}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $vehicleModel->created_at->translatedFormat('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('vehicle-models.edit', $vehicleModel) }}" wire:navigate
                           class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors duration-200 inline-flex items-center"
                           title="Editar">
                            <span class="icon-[tabler--pencil] text-lg"></span>
                        </a>
                        <button wire:click="requestVehicleModelDeletion({{ $vehicleModel->id }})" type="button"
                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1 rounded-md hover:bg-red-100 dark:hover:bg-red-900 transition-colors duration-200 inline-flex items-center ml-1"
                                title="Apagar">
                            <span class="icon-[tabler--trash] text-lg"></span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                        Nenhum modelo encontrado.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{-- Paginação --}}
    @if ($vehicleModels->hasPages())
        <div class="mt-6">
            {{ $vehicleModels->links() }}
        </div>
    @endif
</div>
