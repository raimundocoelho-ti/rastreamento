<div>
    <div class="space-y-4">
        {{-- Nome da Marca --}}
        <div>
            <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Nome da Marca <span class="text-red-500">*</span></label>
            <input type="text" wire:model.defer="name" id="name"
                   class="mt-1 block w-full border {{ $errors->has('name') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 placeholder-neutral-400 dark:placeholder-neutral-500"
                   placeholder="Ex: Ford, Mercedes-Benz, Volvo">
            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
    </div>
</div>
