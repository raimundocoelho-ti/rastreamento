@props(['items' => []])

@if (count($items) > 0)
    @foreach ($items as $item)
        <div class="mb-6">
            {{-- Container Flex para alinhar título/descrição e botão --}}
            {{-- Em telas pequenas (sm), o flex-col empilha o texto e o botão --}}
            {{-- Removido background, shadow, border para não ter "box" --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-3">

                {{-- Bloco do Título e Descrição --}}
                <div class="flex-grow text-center sm:text-left mb-2 sm:mb-0">
                    <h1 class="text-xl sm:text-2xl font-semibold text-neutral-900 dark:text-neutral-100 flex items-center justify-center sm:justify-start">
                        @if(isset($item['icon']))
                            <span class="icon-[{{$item['icon']}}] text-2xl mr-2 text-blue-500 dark:text-blue-400"></span>
                        @endif
                        {{ __($item['label']) }}
                    </h1>
                    @if(isset($item['description']))
                        <p class="text-sm sm:text-base text-neutral-600 dark:text-neutral-400 mt-0.5">
                            {{ __($item['description']) }}
                        </p>
                    @endif
                </div>

                {{-- Bloco do Botão de Ação --}}
                @if(isset($item['route']) && isset($item['labelBtn']))
                    <div class="shrink-0 flex justify-center sm:justify-end">
                        <x-button href="{{ route($item['route']) }}" wire:navigate>
                            @if(isset($item['iconBtn']))
                                <span class="icon-[icon-park-solid--back] mr-1"></span>
                            @elseif(isset($item['icon']))
                                <span class="icon-[{{$item['icon']}}] text-lg mr-2"></span>
                            @endif
                            {{ __($item['labelBtn']) }}
                        </x-button>
                    </div>
                @endif
            </div>

            {{-- Separador após o bloco de cabeçalho/botão. Ajustado o margin top para ser mais compacto --}}
            <flux:separator variant="subtle" class="mt-4 mb-6" />
        </div>
    @endforeach
@endif
