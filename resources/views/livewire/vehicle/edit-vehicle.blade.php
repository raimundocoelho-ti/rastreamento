<?php

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component
{
    use WithFileUploads;

    public Vehicle $vehicle;

    public string $license_plate = '';
    public string $vehicle_type_id = '';
    public string $selected_brand_id = '';
    public string $vehicle_model_id = '';
    public string $color = '';
    public ?int $number_of_seats = null;
    public string $status = '';
    public string $notes = '';
    public $image;
    public ?string $existingImageUrl = null;

    public $vehicleTypes;
    public $allBrands;
    public $vehicleModelsForSelectedBrand;
    public array $headerItems = [];

    public bool $isEditMode = true;
    public ?bool $selectedVehicleTypeRequiresPlate = null;

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
        $this->license_plate = $vehicle->license_plate ?? '';
        $this->vehicle_type_id = (string)$vehicle->vehicle_type_id;
        $this->color = $vehicle->color ?? '';
        $this->number_of_seats = $vehicle->number_of_seats;
        $this->status = $vehicle->status;
        $this->notes = $vehicle->notes ?? '';
        $this->existingImageUrl = $vehicle->image_url;

        $this->vehicleTypes = VehicleType::orderBy('name')->get();
        $this->allBrands = VehicleBrand::orderBy('name')->get();

        if ($this->vehicle->vehicle_model_id) {
            $currentModel = VehicleModel::find($this->vehicle->vehicle_model_id);
            if ($currentModel) {
                $this->selected_brand_id = (string) $currentModel->brand_id;
                $this->vehicleModelsForSelectedBrand = VehicleModel::where('brand_id', $currentModel->brand_id)
                    ->orderBy('name')
                    ->get();
                $this->vehicle_model_id = (string) $currentModel->id;
            } else {
                $this->vehicleModelsForSelectedBrand = collect();
            }
        } else {
            $this->vehicleModelsForSelectedBrand = collect();
        }

        if ($this->vehicle_type_id) {
            $this->updatedVehicleTypeId($this->vehicle_type_id);
        }

        $this->headerItems = [
            [
                'label' => 'Veículo | Editar',
                'description'=> 'Manutenção de Veículos do Sistema',
                'icon' => 'tabler--truck',
                'iconBtn' => 'ion--caret-back',
                'route' => 'vehicles.index',
                'labelBtn' => 'Voltar',
            ]
        ];
    }

    public function updatedSelectedBrandId($brandId): void
    {
        if (!empty($brandId)) {
            $this->vehicleModelsForSelectedBrand = VehicleModel::where('brand_id', $brandId)
                ->orderBy('name')
                ->get();
        } else {
            $this->vehicleModelsForSelectedBrand = collect();
        }
        $this->vehicle_model_id = '';
        $this->resetValidation('vehicle_model_id');
    }

    public function updatedVehicleTypeId($value): void
    {
        if ($value) {
            $type = $this->vehicleTypes->firstWhere('id', (int)$value);
            if ($type) {
                $this->selectedVehicleTypeRequiresPlate = (bool) $type->requires_license_plate;
            }
        } else {
            $this->selectedVehicleTypeRequiresPlate = null;
        }
        $this->resetValidation(['license_plate']);
    }

    public function removeNewImage()
    {
        $this->image = null;
        $this->resetValidation('image');
    }

    public function getRules(): array
    {
        $vehicleId = $this->vehicle->id;
        $rules = [
            'vehicle_type_id' => 'required|integer|exists:vehicle_types,id',
            'selected_brand_id' => 'required|integer|exists:vehicle_brands,id',
            'vehicle_model_id' => 'required|integer|exists:vehicle_models,id',
            'color' => 'nullable|string|max:50',
            'number_of_seats' => 'nullable|integer|min:1|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:2048',
        ];

        if ($this->selectedVehicleTypeRequiresPlate === true) {
            $rules['license_plate'] = "required|string|max:15|unique:vehicles,license_plate,{$vehicleId},id,license_plate,NULL";
        } else {
            $rules['license_plate'] = "nullable|string|max:15|unique:vehicles,license_plate,{$vehicleId},id,license_plate,NULL";
        }
        return $rules;
    }

    public function updateVehicle(): void
    {
        $validatedData = $this->validate($this->getRules());

        $updateData = [
            'license_plate' => $validatedData['license_plate'] ?? null,
            'vehicle_type_id' => $validatedData['vehicle_type_id'],
            'vehicle_model_id' => $validatedData['vehicle_model_id'],
            'color' => $validatedData['color'],
            'number_of_seats' => $validatedData['number_of_seats'],
            'status' => $validatedData['status'],
            'notes' => $validatedData['notes'],
        ];

        if ($this->image) {
            if ($this->vehicle->image) {
                Storage::disk('public')->delete($this->vehicle->image);
            }
            $imageName = Str::uuid() . '.' . $this->image->getClientOriginalExtension();
            $imagePathForDb = 'vehicle_images/' . $imageName;
            $targetDirectory = storage_path('app/public/vehicle_images');
            if (!is_dir($targetDirectory)) {
                @mkdir($targetDirectory, 0775, true);
            }
            try {
                $img = InterventionImage::read($this->image->getRealPath());
                $img->scaleDown(width: 800, height: 600);
                $img->save(storage_path('app/public/' . $imagePathForDb));
                $updateData['image'] = $imagePathForDb;
            } catch (\Exception $e) {
                Log::error("Erro ao processar imagem do veículo (update): " . $e->getMessage());
                session()->flash('error', 'Erro ao processar a nova imagem.');
            }
        }

        $this->vehicle->update($updateData);

        session()->flash('success', 'Veículo atualizado com sucesso.');
        $this->redirectRoute('vehicles.index', navigate: true);
    }
}; ?>

<div>
    <x-header-module :items="$headerItems"/>
    <form wire:submit="updateVehicle">
        <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-none p-6">
            @include('livewire.vehicle._form_vehicle', [
               'vehicleTypes' => $vehicleTypes,
               'allBrands' => $allBrands,
               'vehicleModelsForSelectedBrand' => $vehicleModelsForSelectedBrand,
               'image' => $image,
               'existingImageUrl' => $existingImageUrl,
               'isEditMode' => $isEditMode
           ])

            <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-end">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="updateVehicle, image"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-none shadow-xs disabled:opacity-75 inline-flex items-center">
                        <span wire:loading wire:target="updateVehicle, image" class="icon-[tabler--loader-2] animate-spin w-5 h-5 mr-2"></span>
                        <span wire:loading wire:target="updateVehicle, image">A atualizar...</span>
                        <span wire:loading.remove wire:target="updateVehicle, image"><span class="icon-[tabler--device-floppy] w-5 h-5 mr-2"></span>Atualizar Veículo</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
