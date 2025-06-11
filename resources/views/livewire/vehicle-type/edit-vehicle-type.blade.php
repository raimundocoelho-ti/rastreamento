<?php

use App\Models\VehicleType;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
// Usaremos o método rules() para validação aqui

new #[Layout('layouts.app')] class extends Component
{
    public VehicleType $vehicleType; // Injeção do modelo

    // Propriedades para o formulário
    public string $name = '';
    public string $description = '';
    public bool $requires_license_plate = true;
    public bool $controlled_by_odometer = true;
    public bool $controlled_by_hour_meter = false;
    public array $headerItems = [];

    // Método para definir as regras de validação
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:vehicle_types,name,' . $this->vehicleType->id,
            'description' => 'nullable|string|max:1000',
            'requires_license_plate' => 'boolean',
            'controlled_by_odometer' => 'boolean',
            'controlled_by_hour_meter' => 'boolean',
        ];
    }

    // Mensagens de validação personalizadas (opcional)
    protected function messages(): array
    {
        return [
            'name.required' => 'O nome do tipo de veículo é obrigatório.',
            'name.unique' => 'Este nome de tipo de veículo já existe.',
        ];
    }

    public function mount(VehicleType $vehicleType): void
    {
        $this->vehicleType = $vehicleType; // Atribui o tipo de veículo carregado
        $this->name = $vehicleType->name;
        $this->description = $vehicleType->description ?? '';
        $this->requires_license_plate = $vehicleType->requires_license_plate;
        $this->controlled_by_odometer = $vehicleType->controlled_by_odometer;
        $this->controlled_by_hour_meter = $vehicleType->controlled_by_hour_meter;

        $this->headerItems = [
            [
                'label' => 'Departamento | Editar',
                'description'=> 'Manutenção de Departamentos do Sistema',
                'icon' => 'tabler--building-community',
                'iconBtn' => 'ion--caret-back',
                'route' => 'departments.index',
                'labelBtn' => 'Voltar',
            ]
        ];
    }

    public function updateVehicleType(): void
    {
        $validatedData = $this->validate(); // Usa o método rules()

        $this->vehicleType->update($validatedData);

        session()->flash('success', 'Tipo de Veículo "' . htmlspecialchars($this->vehicleType->name) . '" atualizado com sucesso.');
        $this->redirectRoute('vehicle-types.index', navigate: true);
    }
}; ?>

<div>
    <x-header-module :items="$headerItems" />


    <form wire:submit="updateVehicleType">
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
                            wire:target="updateVehicleType"
                            class="bg-neutral-900 hover:bg-neutral-700 text-white text-sm font-medium py-1.5 px-3 rounded-none shadow-xs disabled:opacity-75 inline-flex items-center">
                        <span wire:loading wire:target="updateVehicleType" class="icon-[tabler--loader-2] animate-spin w-4 h-4 mr-2"></span>
                        <span wire:loading wire:target="updateVehicleType">A salvar...</span>
                        <span wire:loading.remove wire:target="updateVehicleType">
        <span class="icon-[tabler--device-floppy] w-4 h-4 mr-2"></span>Salvar
    </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
