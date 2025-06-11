<?php

use App\Models\VehicleBrand;
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

    public array $headerItems = [];

    public function mount(): void
    {
        $this->headerItems = [
            [
                'label' => 'Marcas de Veículo | Listagem',
                'description'=> 'Manutenção de Departamentos do Sistema',
                'icon' => 'tabler--tags',
                'route' => 'brands.create',
                'labelBtn' => 'Novo',
            ]
        ];

    }

    public function updatedSearchName(): void
    {
        $this->resetPage();
    }

    public function requestBrandDeletion(int $brandId): void
    {
        $brand = VehicleBrand::find($brandId);
        if (!$brand) {
            session()->flash('error', 'Marca não encontrada.');
            return;
        }

        // Verifica se a marca está em uso por algum veículo
        if ($brand->vehicles()->exists()) {
            session()->flash('error', 'A marca "' . htmlspecialchars($brand->name) . '" está em uso por um ou mais veículos e não pode ser apagada.');
            return;
        }

        $this->dispatch(
            'openConfirmationModal',
            title: 'Confirmar Exclusão de Marca',
            message: 'Tem a certeza que quer apagar a marca "' . htmlspecialchars($brand->name) . '"? Esta ação não pode ser revertida.',
            eventToDispatchOnConfirm: 'deleteBrandConfirmed',
            eventParams: ['brandId' => $brandId],
            confirmButtonText: 'Sim, Apagar',
            cancelButtonText: 'Cancelar'
        );
    }

    #[On('deleteBrandConfirmed')]
    public function deleteBrandConfirmed(int $brandId): void
    {
        $brand = VehicleBrand::find($brandId);

        if (!$brand) {
            session()->flash('error', 'Marca não encontrada ou já foi apagada.');
            return;
        }

        // Re-verificar dependências antes de apagar (boa prática)
        if ($brand->vehicles()->exists()) {
            session()->flash('error', 'A marca "' . htmlspecialchars($brand->name) . '" está em uso e não pode ser apagada. Verifique os veículos associados.');
            return;
        }

        $brandName = $brand->name;
        if ($brand->delete()) {
            session()->flash('success', 'Marca "' . htmlspecialchars($brandName) . '" apagada com sucesso.');
        } else {
            session()->flash('error', 'Ocorreu um erro ao tentar apagar a marca.');
        }
    }

    public function with(): array
    {
        $brandsQuery = VehicleBrand::query()
            ->when($this->searchName, function ($query) {
                $query->where('name', 'like', '%' . $this->searchName . '%');
            });

        $brands = $brandsQuery->orderBy('name')->paginate(10); // Ou o número de itens por página que desejar

        return [
            'brands' => $brands,
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

    {{-- Bloco de Pesquisa --}}
    {{-- Compacto, com campo de input, ícone de lupa e label oculta para acessibilidade --}}
    <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-md p-3 mb-6">
        <label for="searchName" class="sr-only">Pesquisar por Nome</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="icon-[tabler--search] text-neutral-400 dark:text-neutral-500 text-lg"></span>
            </div>
            <input type="text" wire:model.live.debounce.300ms="searchName" id="searchName"
                   placeholder="Pesquisar por nome da marca..."
                   class="block w-full pl-10 pr-3 py-2 border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md placeholder-neutral-400 dark:placeholder-neutral-500">
        </div>
    </div>

    {{-- Tabela de Listagem --}}
    {{-- Com linhas compactas, estilo profissional e ações bem alinhadas --}}
    <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-md overflow-hidden">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-700">
            <tr>
                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                    Nome da Marca
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
            @forelse ($brands as $brand)
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700">
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $brand->name }}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $brand->created_at->translatedFormat('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('brands.edit', $brand) }}" wire:navigate
                           class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors duration-200 inline-flex items-center"
                           title="Editar">
                            <span class="icon-[tabler--pencil] text-lg"></span>
                        </a>
                        <button wire:click="requestBrandDeletion({{ $brand->id }})" type="button"
                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1 rounded-md hover:bg-red-100 dark:hover:bg-red-900 transition-colors duration-200 inline-flex items-center ml-1"
                                title="Apagar">
                            <span class="icon-[tabler--trash] text-lg"></span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                        Nenhuma marca encontrada.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{-- Paginação --}}
    @if ($brands->hasPages())
        <div class="mt-6">
            {{ $brands->links() }}
        </div>
    @endif
</div>
