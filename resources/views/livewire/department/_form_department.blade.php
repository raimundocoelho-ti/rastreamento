<div>
    <div class="flex flex-col lg:flex-row gap-x-8 gap-y-6">

        <div class="flex-grow lg:w-2/3 space-y-8">

            <section>
                <h3 class="text-lg font-semibold leading-tight text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-700 pb-2">
                    Detalhes do Departamento
                </h3>
                <div class="space-y-6">
                    {{-- Nome do Departamento --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Nome do Departamento</label>
                        <input type="text" wire:model.defer="name" id="name"
                               class="mt-1 block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 placeholder-neutral-400 dark:placeholder-neutral-500">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Gestor do Departamento --}}
                    <div>
                        <label for="manager_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Gestor Responsável</label>
                        <select wire:model.defer="manager_id" id="manager_id"
                                class="mt-1 block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 appearance-none">
                            <option value="">Nenhum gestor selecionado</option>
                            @if(isset($managers) && count($managers) > 0)
                                @foreach ($managers as $manager)
                                    <option value="{{ $manager['id'] }}">{{ $manager['name'] }} ({{ $manager['email'] }})</option>
                                @endforeach
                            @else
                                <option value="" disabled>Nenhum gestor disponível</option>
                            @endif
                        </select>
                        @error('manager_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Descrição --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Descrição</label>
                        <textarea wire:model.defer="description" id="description" rows="4"
                                  class="mt-1 block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-none p-2 placeholder-neutral-400 dark:placeholder-neutral-500"></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>
        </div>

        <div class="lg:w-1/3 space-y-6">
            <section>
                <h3 class="text-lg font-semibold leading-tight text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-700 pb-2">
                    Imagem do Departamento
                </h3>
                <div class="flex flex-col items-center space-y-4 p-4 border border-dashed border-neutral-300 dark:border-neutral-600 rounded-none bg-white dark:bg-neutral-800/30 min-h-[200px] justify-center">
                    {{-- Pré-visualização da Imagem --}}
                    <div class="w-32 h-32 rounded-md overflow-hidden bg-neutral-100 dark:bg-neutral-700 flex items-center justify-center text-neutral-400 dark:text-neutral-500 border border-neutral-200 dark:border-neutral-600">
                        @if ($image && method_exists($image, 'temporaryUrl'))
                            <img src="{{ $image->temporaryUrl() }}" alt="Pré-visualização da nova imagem" class="w-full h-full object-contain"> {{-- object-contain para logos --}}
                        @elseif ($existingImageUrl)
                            <img src="{{ $existingImageUrl }}" alt="Imagem Atual do Departamento" class="w-full h-full object-contain"> {{-- object-contain para logos --}}
                        @else
                            {{-- Placeholder SVG Icon (Image/Department Icon) --}}
                            <span class="icon-[tabler--photo] text-5xl opacity-50"></span>
                        @endif
                    </div>

                    {{-- Input de Ficheiro Estilizado --}}
                    <div class="flex flex-col items-center w-full">
                        <label for="department-image-upload-button"
                               class="cursor-pointer inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-none shadow-sm text-white bg-neutral-900 hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral-500 dark:focus:ring-offset-neutral-800">
                            <span class="icon-[tabler--upload] w-4 h-4 mr-2"></span>
                            {{ ($existingImageUrl || ($image && method_exists($image, 'temporaryUrl'))) ? 'Alterar Imagem' : 'Carregar Imagem' }}
                        </label>

                        <input type="file" wire:model="image" id="department-image-upload-button" class="sr-only">

                        <div wire:loading wire:target="image" class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">A carregar...</div>

                        @error('image')
                        <span class="block text-red-500 text-xs mt-1 text-center">{{ $message }}</span>
                        @enderror

                        {{-- Botão para remover nova imagem selecionada --}}
                        @if ($image && method_exists($image, 'temporaryUrl'))
                            <button type="button"
                                    wire:click="removeNewImage"
                                    class="mt-3 text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                    title="Cancelar seleção da nova imagem">
                                Cancelar nova imagem
                            </button>
                        @endif
                    </div>

                </div>
            </section>
        </div>
    </div>
</div>
