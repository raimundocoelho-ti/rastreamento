<?php

use App\Models\VehicleType;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new #[Layout('layouts.app')] class extends Component
{
    #[Validate('required|string|max:255|unique:vehicle_types,name')]
    public string $name = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('boolean')]
    public bool $requires_license_plate = true; // Default da migração

    #[Validate('boolean')]
    public bool $controlled_by_odometer = true; // Default da migração

    #[Validate('boolean')]
    public bool $controlled_by_hour_meter = false; // Default da migração

    public array $headerItems = [];

    public function mount(): void
    {
        $this->headerItems = [
            [
                'label' => 'Tipos de Veículos | Cadastrar',
                'description'=> 'Manutenção de Usuários do Sistema',
                'icon' => 'tabler--category-2',
                'iconBtn' => 'ion--caret-back',
                'route' => 'vehicle-types.index',
                'labelBtn' => 'Voltar',
            ]
        ];
    }

    public function saveVehicleType(): void
    {
        $validatedData = $this->validate();

        VehicleType::create($validatedData);

        session()->flash('success', 'Tipo de Veículo "' . htmlspecialchars($this->name) . '" criado com sucesso.');
        $this->redirectRoute('vehicle-types.index', navigate: true);
    }

}; ?>

<div>
    <x-header-module :items="$headerItems" />

    <form wire:submit="saveVehicleType">
        <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-none p-6">

            @include('livewire.vehicle-type._form_vehicle_type')

            <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-end">


                    <a href="{{ route('vehicle-types.index') }}"
                       wire:navigate
                       class="bg-neutral-200 hover:bg-neutral-300 mr-2 text-ba text-neutral-900 px-3 py-1.5 rounded-none shadow-xs inline-flex items-center">
                        <span class="icon-[tabler--x] w-4 h-4 mr-2"></span>
                        Cancelar
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveVehicleType"
                            class="bg-neutral-900 hover:bg-neutral-700 text-white font-medium text-sm py-1.5 px-3 rounded-none shadow-xs disabled:opacity-75 inline-flex items-center">
                        <span wire:loading wire:target="saveVehicleType"
                              class="icon-[tabler--loader-2] animate-spin w-4 h-4 mr-2"></span>
                        <span wire:loading wire:target="saveVehicleType">A salvar...</span>
                        <span wire:loading.remove wire:target="saveVehicleType">
        <span class="icon-[tabler--device-floppy] w-4 h-4 mr-2"></span>
        Salvar
    </span>
                    </button>


                </div>
            </div>
        </div>
    </form>
</div>
