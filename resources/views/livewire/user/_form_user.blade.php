<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="space-y-10">
        <section>
            <h3 class="text-lg font-semibold leading-tight text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-700 pb-2">
                Dados do Usuário
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Nome Completo <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="name" id="name"
                           class="block w-full border {{ $errors->has('name') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md p-2 placeholder-neutral-400 dark:placeholder-neutral-500">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" wire:model.defer="email" id="email"
                           class="block w-full border {{ $errors->has('email') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md p-2 placeholder-neutral-400 dark:placeholder-neutral-500">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Função <span class="text-red-500">*</span></label>
                    <select wire:model.defer="role" id="role"
                            class="block w-full border {{ $errors->has('role') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md p-2 appearance-none">
                        <option value="">Selecione uma função...</option>
                        @if(isset($allRoles))
                            @foreach ($allRoles as $roleValue => $roleLabel)
                                <option value="{{ $roleValue }}">{{ $roleLabel }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-lg font-semibold leading-tight text-neutral-800 dark:text-neutral-200 mb-4 mt-4 border-b border-neutral-200 dark:border-neutral-700 pb-2">
                Senha
                @if($isEditMode)
                    <span class="text-xs font-normal text-neutral-500 dark:text-neutral-400">(Deixe em branco para não alterar)</span>
                @endif
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Nova Senha @if(!$isEditMode)<span class="text-red-500">*</span>@endif</label>
                    <input type="password" wire:model.defer="password" id="password"
                           class="block w-full border {{ $errors->has('password') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md p-2">
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Confirmar Nova Senha @if(!$isEditMode)<span class="text-red-500">*</span>@endif</label>
                    <input type="password" wire:model.defer="password_confirmation" id="password_confirmation"
                           class="block w-full border {{ $errors->has('password_confirmation') ? 'border-red-500' : 'border-neutral-300 dark:border-neutral-600' }} bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md p-2">
                    @error('password_confirmation') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>
    </div>
</div>
