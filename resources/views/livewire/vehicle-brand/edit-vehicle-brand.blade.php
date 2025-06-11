<?php

use App\Models\VehicleBrand;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component
{
    public VehicleBrand $brand; // Injeção do modelo Brand

    public string $name = '';
    public array $headerItems = [];

    // Método para definir as regras de validação dinamicamente
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:vehicle_brands,name,' . $this->brand->id,
        ];
    }

    public function mount(VehicleBrand $brand): void
    {
        $this->brand = $brand;
        $this->name = $brand->name;

        $this->headerItems = [
            [
                'label' => 'Marca do Veículo | Editar',
                'description' => 'Manutenção de Departamentos do Sistema',
                'icon' => 'tabler--tags',
                'iconBtn' => 'ion--caret-back',
                'route' => 'brands.index',
                'labelBtn' => 'Voltar',
            ]
        ];

    }

    public function updateBrand(): void
    {
        $validatedData = $this->validate($this->rules()); // Valida usando o método rules()

        $this->brand->update($validatedData);

        session()->flash('success', 'Marca "' . htmlspecialchars($this->brand->name) . '" atualizada com sucesso.');
        $this->redirectRoute('brands.index', navigate: true);
    }
}; ?>

<div>
    <x-header-module :items="$headerItems"/>

    <form wire:submit="updateBrand">
        <div class="bg-white dark:bg-neutral-800 shadow-sm border border-neutral-200 dark:border-neutral-700 rounded-none p-6">
            @include('livewire.vehicle-brand._form_brand')

            <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-end">

                    <a href="{{ route('brands.index') }}"
                       wire:navigate
                       class="bg-neutral-200 hover:bg-neutral-300 mr-2 text-ba text-neutral-900 px-3 py-1.5 rounded-none shadow-sm inline-flex items-center">
                        <span class="icon-[tabler--x] w-4 h-4 mr-2"></span>
                        Cancelar
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="updateBrand"
                            class="bg-neutral-900 hover:bg-neutral-700 text-white font-medium text-sm py-1.5 px-3 rounded-none shadow-sm disabled:opacity-75 inline-flex items-center">
                        <span wire:loading wire:target="updateBrand"
                              class="icon-[tabler--loader-2] animate-spin w-4 h-4 mr-2"></span>
                        <span wire:loading wire:target="updateBrand">A salvar...</span>
                        <span wire:loading.remove wire:target="updateBrand">
        <span class="icon-[tabler--device-floppy] w-4 h-4 mr-2"></span>
        Salvar
    </span>
                    </button>


                </div>
            </div>
        </div>
    </form>
</div>
