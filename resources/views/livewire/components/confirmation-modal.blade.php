<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

new class extends Component
{
    public bool $show = false;
    public string $title = 'Confirmar Ação';
    public string $message = 'Tem a certeza?';
    public string $confirmButtonText = 'Confirmar';
    public string $cancelButtonText = 'Cancelar';

    // Alterar para public
    public string $eventToDispatchOnConfirm = '';
    public array $eventParams = [];

    #[On('openConfirmationModal')]
    public function openModal(string $title, string $message, string $eventToDispatchOnConfirm, array $eventParams = [], string $confirmButtonText = 'Confirmar', string $cancelButtonText = 'Cancelar'): void
    {
        // Log para ver os parâmetros recebidos (podes manter ou remover após confirmar que funciona)
        Log::info('Modal openModal method: PARÂMETROS RECEBIDOS', [
            'title_received' => $title,
            'message_received' => $message,
            'eventToDispatchOnConfirm_received' => $eventToDispatchOnConfirm,
            'eventParams_received' => $eventParams,
            'confirmButtonText_received' => $confirmButtonText,
            'cancelButtonText_received' => $cancelButtonText,
        ]);

        $this->title = $title;
        $this->message = $message;
        $this->eventToDispatchOnConfirm = $eventToDispatchOnConfirm;
        $this->eventParams = $eventParams;
        $this->confirmButtonText = $confirmButtonText;
        $this->cancelButtonText = $cancelButtonText;
        $this->show = true;

        // Log para ver o estado das propriedades após serem definidas (podes manter ou remover)
        Log::info('Modal openModal method: ESTADO INTERNO APÓS DEFINIÇÃO', [
            'internal_eventToDispatchOnConfirm' => $this->eventToDispatchOnConfirm,
            'internal_eventParams' => $this->eventParams,
            'is_shown' => $this->show
        ]);
    }

    public function confirm(): void
    {
        // Log para depuração (podes manter ou remover)
        Log::info('Modal confirm method: dispatching event', [
            'event' => $this->eventToDispatchOnConfirm,
            'params' => $this->eventParams
        ]);

        if ($this->eventToDispatchOnConfirm && !empty($this->eventToDispatchOnConfirm)) {
            $this->dispatch($this->eventToDispatchOnConfirm, ...$this->eventParams);
        } else {
            Log::error('Modal confirm method: Tentativa de dispatch com eventToDispatchOnConfirm VAZIO OU NULO.');
        }
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->show = false;
        $this->resetState();
    }

    protected function resetState(): void
    {
        // Não é estritamente necessário resetar as propriedades públicas aqui se elas
        // são sempre explicitamente definidas em openModal(). Mas pode ser uma boa prática.
        $this->title = 'Confirmar Ação';
        $this->message = 'Tem a certeza?';
        $this->confirmButtonText = 'Confirmar';
        $this->cancelButtonText = 'Cancelar';
        $this->eventToDispatchOnConfirm = '';
        $this->eventParams = [];
    }
}; ?>




<div
    x-data="{ showModal: @entangle('show').live }"
    x-show="showModal"
    x-on:keydown.escape.window="if(showModal) { showModal = false; $wire.closeModal(); }"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
    style="display: none;"
    x-cloak
>
    <div x-show="showModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/70 dark:bg-black/80"
         wire:click="closeModal"
         aria-hidden="true">
    </div>

    <div x-show="showModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="bg-white dark:bg-neutral-800 rounded-none shadow-xl w-full max-w-lg mx-auto transform transition-all relative z-10"
    >
        <div class="p-6">
            <div class="flex items-start">
                <div class="mt-0 text-left w-full">
                    <h3 class="text-lg font-semibold leading-6 text-neutral-900 dark:text-neutral-100" id="modal-title">
                        {{ $title }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-neutral-600 dark:text-neutral-300">
                            {{ $message }}
                        </p>
                    </div>
                </div>
                <button wire:click="closeModal" type="button"
                        class="ml-4 p-1 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="bg-neutral-50 dark:bg-neutral-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-none">
            <button wire:click="confirm" type="button"
                    class="w-full inline-flex justify-center rounded-none border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                    wire:loading.attr="disabled" wire:target="confirm">
                <span wire:loading.remove wire:target="confirm">{{ $confirmButtonText }}</span>
                <span wire:loading wire:target="confirm">Aguarde...</span>
            </button>
            <button wire:click="closeModal" type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-none border border-neutral-300 dark:border-neutral-500 shadow-sm px-4 py-2 bg-white dark:bg-neutral-600 text-base font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral-500 sm:mt-0 sm:w-auto sm:text-sm"
                    wire:loading.attr="disabled" wire:target="confirm">
                {{ $cancelButtonText }}
            </button>
        </div>
    </div>
</div>
