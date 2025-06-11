<div>
    <div class="space-y-8">
        <section>
            <h3 class="text-base font-semibold leading-tight text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-700 pb-2">
                Detalhes do Tipo de Veículo
            </h3>
            <div class="space-y-6">
                {{-- Nome do Tipo de Veículo --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Nome do Tipo de Veículo</label>
                    <input type="text" wire:model.defer="name" id="name"
                           class="mt-1 block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md p-2 placeholder-neutral-400 dark:placeholder-neutral-500">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Descrição --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Descrição</label>
                    <textarea wire:model.defer="description" id="description" rows="4"
                              class="mt-1 block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md p-2 placeholder-neutral-400 dark:placeholder-neutral-500"></textarea>
                    @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-base font-semibold leading-tight text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-700 pb-2">
                Configurações de Controlo
            </h3>
            <div class="space-y-6">
                {{-- Requer Matrícula --}}
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="requires_license_plate" wire:model.defer="requires_license_plate" type="checkbox"
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-neutral-300 dark:border-neutral-600 rounded-xs bg-white dark:bg-neutral-700 checked:bg-neutral-900 dark:checked:bg-blue-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="requires_license_plate" class="font-medium text-neutral-700 dark:text-neutral-300">Requer Matrícula</label>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Indica se veículos deste tipo devem ter uma matrícula associada.</p>
                    </div>
                    @error('requires_license_plate') <span class="text-red-500 text-xs mt-1 ml-3">{{ $message }}</span> @enderror
                </div>

                {{-- Controlado por Odómetro --}}
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="controlled_by_odometer" wire:model.defer="controlled_by_odometer" type="checkbox"
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-neutral-300 dark:border-neutral-600 rounded-xs bg-white dark:bg-neutral-700 checked:bg-neutral-900 dark:checked:bg-blue-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="controlled_by_odometer" class="font-medium text-neutral-700 dark:text-neutral-300">Controlado por Odómetro</label>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Indica se o controlo de utilização é feito através de odómetro (kms).</p>
                    </div>
                    @error('controlled_by_odometer') <span class="text-red-500 text-xs mt-1 ml-3">{{ $message }}</span> @enderror
                </div>

                {{-- Controlado por Horímetro --}}
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="controlled_by_hour_meter" wire:model.defer="controlled_by_hour_meter" type="checkbox"
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-neutral-300 dark:border-neutral-600 rounded-xs bg-white dark:bg-neutral-700 checked:bg-neutral-900 dark:checked:bg-blue-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="controlled_by_hour_meter" class="font-medium text-neutral-700 dark:text-neutral-300">Controlado por Horímetro</label>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Indica se o controlo de utilização é feito através de horímetro (horas de uso).</p>
                    </div>
                    @error('controlled_by_hour_meter') <span class="text-red-500 text-xs mt-1 ml-3">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>
    </div>
</div>
