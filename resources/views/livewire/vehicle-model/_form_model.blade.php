<div>
    <div class="space-y-4">
        {{-- Seleção da Marca --}}
        <div>
            <label for="brand_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Marca <span class="text-red-500">*</span></label>
            <select wire:model.defer="brand_id" id="brand_id"
                    class="mt-1 block w-full border {{ $errors->has('brand_id') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 appearance-none">
                <option value="">Selecione uma marca...</option>
                @if(isset($brands) && count($brands) > 0)
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                @else
                    <option value="" disabled>Nenhuma marca cadastrada</option>
                @endif
            </select>
            @error('brand_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Nome do Modelo --}}
        <div>
            <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Nome do Modelo <span class="text-red-500">*</span></label>
            <input type="text" wire:model.defer="name" id="name"
                   class="mt-1 block w-full border {{ $errors->has('name') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 placeholder-neutral-400 dark:placeholder-neutral-500"
                   placeholder="Ex: Gol, Hilux, FH 540">
            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
    </div>
</div>
