<div>
    <div class="space-y-8">
        <section>
            <h3 class="text-lg font-semibold leading-tight text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-700 pb-2">
                Dados Principais do Veículo
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                <div>
                    <label for="vehicle_type_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <select wire:model.live="vehicle_type_id" id="vehicle_type_id"
                            class="mt-1 block w-full border {{ $errors->has('vehicle_type_id') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 appearance-none">
                        <option value="">Selecione...</option>
                        @if(isset($vehicleTypes))
                            @foreach ($vehicleTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('vehicle_type_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="license_plate" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Placa</label>
                    <input type="text" wire:model.defer="license_plate" id="license_plate" placeholder="AAA-0A00"
                           class="mt-1 block w-full border {{ $errors->has('license_plate') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 placeholder-neutral-400 dark:placeholder-neutral-500">
                    @error('license_plate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Status <span class="text-red-500">*</span></label>
                    <select wire:model.defer="status" id="status"
                            class="mt-1 block w-full border {{ $errors->has('status') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 appearance-none">
                        <option value="Ativo">Ativo</option>
                        <option value="Em Manutenção">Em Manutenção</option>
                        <option value="Inativo">Inativo</option>
                        <option value="Baixado">Baixado</option>
                    </select>
                    @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="selected_brand_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Marca <span class="text-red-500">*</span></label>
                    <select wire:model.live="selected_brand_id" id="selected_brand_id"
                            class="mt-1 block w-full border {{ $errors->has('selected_brand_id') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 appearance-none">
                        <option value="">Selecione a marca...</option>
                        @if(isset($allBrands))
                            @foreach ($allBrands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('selected_brand_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="vehicle_model_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Modelo <span class="text-red-500">*</span></label>
                    <select wire:model.defer="vehicle_model_id" id="vehicle_model_id"
                            class="mt-1 block w-full border {{ $errors->has('vehicle_model_id') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 appearance-none"
                        {{ !isset($vehicleModelsForSelectedBrand) || $vehicleModelsForSelectedBrand->isEmpty() ? 'disabled' : '' }}>
                        <option value="">Selecione o modelo...</option>
                        @if(isset($vehicleModelsForSelectedBrand))
                            @foreach ($vehicleModelsForSelectedBrand as $model)
                                <option value="{{ $model->id }}">{{ $model->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('vehicle_model_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="color" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Cor</label>
                    <input type="text" wire:model.defer="color" id="color"
                           class="mt-1 block w-full border {{ $errors->has('color') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 placeholder-neutral-400 dark:placeholder-neutral-500">
                    @error('color') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="number_of_seats" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Nº de Lugares (c/ motorista)</label>
                    <input type="number" wire:model.defer="number_of_seats" id="number_of_seats" min="1"
                           class="mt-1 block w-full border {{ $errors->has('number_of_seats') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 placeholder-neutral-400 dark:placeholder-neutral-500">
                    @error('number_of_seats') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-lg font-semibold leading-tight text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-700 pb-2">
                Observações
            </h3>
            <div>
                <label for="notes" class="sr-only">Observações</label>
                <textarea wire:model.defer="notes" id="notes" rows="4"
                          class="mt-1 block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 placeholder-neutral-400 dark:placeholder-neutral-500"></textarea>
                @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </section>

        <section>
            <h3 class="text-lg font-semibold leading-tight text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-700 pb-2">
                Imagem do Veículo
            </h3>
            <div class="flex flex-col items-center space-y-4 p-4 border border-dashed border-neutral-300 dark:border-neutral-600 rounded-none bg-white dark:bg-neutral-800/30 min-h-[200px] justify-center md:w-2/3 lg:w-1/2 mx-auto">
                <div class="w-full max-w-[300px] h-48 rounded-md overflow-hidden bg-neutral-100 dark:bg-neutral-700 flex items-center justify-center text-neutral-400 dark:text-neutral-500 border border-neutral-200 dark:border-neutral-600">
                    @if ($image && method_exists($image, 'temporaryUrl'))
                        <img src="{{ $image->temporaryUrl() }}" alt="Pré-visualização da nova imagem" class="w-full h-full object-contain">
                    @elseif (isset($existingImageUrl) && $existingImageUrl)
                        <img src="{{ $existingImageUrl }}" alt="Imagem Atual do Veículo" class="w-full h-full object-contain">
                    @else
                        <span class="icon-[tabler--photo] text-6xl opacity-50"></span>
                    @endif
                </div>
                <div class="flex flex-col items-center w-full">
                    <label for="vehicle-image-upload-button" class="cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-none shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-neutral-800">
                        <span class="icon-[tabler--upload] w-4 h-4 mr-2"></span>
                        {{ (isset($existingImageUrl) && $existingImageUrl) || ($image && method_exists($image, 'temporaryUrl')) ? 'Alterar Imagem' : 'Carregar Imagem' }}
                    </label>
                    <input type="file" wire:model="image" id="vehicle-image-upload-button" class="sr-only">
                    <div wire:loading wire:target="image" class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">A carregar...</div>
                    @error('image') <span class="block text-red-500 text-xs mt-1 text-center">{{ $message }}</span> @enderror

                    @if ($image && method_exists($image, 'temporaryUrl'))
                        <button type="button" wire:click="removeNewImage" class="mt-3 text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" title="Cancelar seleção da nova imagem">
                            Cancelar nova imagem
                        </button>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>
