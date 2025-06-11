<?php

use App\Models\VehicleModel;
use App\Models\VehicleBrand; // Importar VehicleBrand
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate; // Usaremos Validate para regras estáticas

new #[Layout('layouts.app')] class extends Component
{
    // Validação estática para brand_id
    #[Validate('required|integer|exists:vehicle_brands,id', message: [
        'brand_id.required' => 'O campo marca é obrigatório.',
        'brand_id.exists' => 'A marca selecionada é inválida.',
    ])]
    public string $brand_id = '';
    public array $headerItems = [];

    // Validação estática para name (sem a regra unique aqui, pois ela é dinâmica)
    #[Validate('required|string|max:100', message: [
        'name.required' => 'O nome do modelo é obrigatório.',
        'name.max' => 'O nome do modelo não pode ter mais de 100 caracteres.',
    ])]
    public string $name = '';

    public $brands; // Para popular o select de marcas

    public function mount(): void
    {
        $this->brands = VehicleBrand::orderBy('name')->get();

        $this->headerItems = [
            [
                'label' => 'Modelo do Veículo | Cadastrar',
                'description' => 'Manutenção de modelo do veículo do Sistema',
                'icon' => 'tabler--tags',
                'iconBtn' => 'tabler--assembly',
                'route' => 'vehicle-models.index',
                'labelBtn' => 'Voltar',
            ]
        ];
    }

    public function saveVehicleModel(): void
    {
        // Validar primeiro as regras estáticas
        $this->validateOnly('brand_id');
        $this->validateOnly('name');

        // Agora, validar a regra de unicidade dinâmica para 'name'
        // A regra deve ser construída com o valor atual de $this->brand_id
        $this->validate(
            ['name' => 'unique:vehicle_models,name,NULL,id,brand_id,' . $this->brand_id],
            ['name.unique' => 'Este nome de modelo já existe para a marca selecionada.']
        );

        VehicleModel::create([
            'name' => $this->name,
            'brand_id' => $this->brand_id, // Incluir brand_id
        ]);

        session()->flash('success', 'Modelo "' . htmlspecialchars($this->name) . '" criado com sucesso.');
        $this->redirectRoute('vehicle-models.index', navigate: true);
    }
}; ?>

<div>
    <x-header-module :items="$headerItems"/>

    <form wire:submit="saveVehicleModel">
        <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-none p-6">
            @include('livewire.vehicle-model._form_model', ['brands' => $brands])

            <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-end">

                    <a href="{{ route('vehicle-models.index') }}"
                       wire:navigate
                       class="bg-neutral-200 hover:bg-neutral-300 mr-2 text-ba text-neutral-900 px-3 py-1.5 rounded-none shadow-xs inline-flex items-center">
                        <span class="icon-[tabler--x] w-4 h-4 mr-2"></span>
                        Cancelar
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveVehicleModel"
                            class="bg-neutral-900 hover:bg-neutral-700 text-white font-medium text-sm py-1.5 px-3 rounded-none shadow-xs disabled:opacity-75 inline-flex items-center">
                        <span wire:loading wire:target="saveVehicleModel"
                              class="icon-[tabler--loader-2] animate-spin w-4 h-4 mr-2"></span>
                        <span wire:loading wire:target="saveVehicleModel">A salvar...</span>
                        <span wire:loading.remove wire:target="saveVehicleModel">
        <span class="icon-[tabler--device-floppy] w-4 h-4 mr-2"></span>
        Salvar
    </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
