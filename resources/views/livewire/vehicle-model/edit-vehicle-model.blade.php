<?php

use App\Models\VehicleModel;
use App\Models\VehicleBrand;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component
{
    public VehicleModel $vehicleModel;

    public string $brand_id = '';
    public string $name = '';
    public array $headerItems = [];
    public $brands; // Alterado de array para mixed, ou pode ser Collection

    protected function rules(): array
    {
        return [
            'brand_id' => 'required|integer|exists:vehicle_brands,id',
            'name' => 'required|string|max:100|unique:vehicle_models,name,' . $this->vehicleModel->id . ',id,brand_id,' . $this->brand_id,
        ];
    }

    public function mount(VehicleModel $vehicleModel): void
    {
        $this->vehicleModel = $vehicleModel;
        $this->name = $vehicleModel->name ?? '';
        $this->brand_id = (string) $vehicleModel->brand_id;
        $this->brands = VehicleBrand::orderBy('name')->get(); // REMOVIDO ->toArray()

        $this->headerItems = [
            [
                'label' => 'Modelo do Veículo | Editar',
                'description' => 'Manutenção de modelo do veículo do Sistema',
                'icon' => 'tabler--tags',
                'iconBtn' => 'tabler--assembly',
                'route' => 'vehicle-models.index',
                'labelBtn' => 'Voltar',
            ]
        ];
    }

    public function updateVehicleModel(): void
    {
        $validatedData = $this->validate([
            'brand_id' => 'required|integer|exists:vehicle_brands,id',
            'name' => 'required|string|max:100|unique:vehicle_models,name,' . $this->vehicleModel->id . ',id,brand_id,' . $this->brand_id,
        ]);

        $this->vehicleModel->update($validatedData);

        session()->flash('success', 'Modelo "' . htmlspecialchars($this->vehicleModel->name) . '" atualizado com sucesso.');
        $this->redirectRoute('vehicle-models.index', navigate: true);
    }
}; ?>


<div>
    <x-header-module :items="$headerItems" />

    <form wire:submit="updateVehicleModel">
        <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-none p-6">
            {{-- Passar a variável $brands para o formulário parcial --}}
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
                            wire:target="updateVehicleModel"
                            class="bg-neutral-900 hover:bg-neutral-700 text-white font-medium text-sm py-1.5 px-3 rounded-none shadow-xs disabled:opacity-75 inline-flex items-center">
                        <span wire:loading wire:target="updateVehicleModel"
                              class="icon-[tabler--loader-2] animate-spin w-4 h-4 mr-2"></span>
                        <span wire:loading wire:target="updateVehicleModel">A salvar...</span>
                        <span wire:loading.remove wire:target="updateVehicleModel">
        <span class="icon-[tabler--device-floppy] w-4 h-4 mr-2"></span>
        Salvar
    </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
