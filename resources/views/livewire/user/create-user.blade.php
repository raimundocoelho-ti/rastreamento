<?php

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $role = '';
    public string $password = '';
    public string $password_confirmation = '';

    public array $allRoles = [];
    public bool $isEditMode = false;
    public ?string $existingImageUrl = null;
    public array $headerItems = [];

    public function mount(): void
    {
        $this->allRoles = collect(UserRole::cases())->mapWithKeys(function ($role) {
            return [$role->value => Str::title($role->value)];
        })->all();
        $this->role = UserRole::OPERATOR->value;

        $this->headerItems = [
            [
                'label' => 'Usuários | Cadastrar',
                'description'=> 'Manutenção de Usuários do Sistema',
                'icon' => 'tabler--users',
                'iconBtn' => 'ion--caret-back',
                'route' => 'users.index',
                'labelBtn' => 'Voltar',
            ]
        ];
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => ['required', 'string', new \Illuminate\Validation\Rules\Enum(UserRole::class)],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ];
    }


    public function saveUser(): void
    {
        $validatedData = $this->validate();

        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'password' => Hash::make($validatedData['password']),
        ]);

        session()->flash('success', 'Usuário "' . htmlspecialchars($validatedData['name']) . '" criado com sucesso.');
        $this->redirectRoute('users.index', navigate: true);
    }
}; ?>


<div>
    <x-header-module :items="$headerItems" />

    <form wire:submit="saveUser">
        <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-none p-6">
            @include('livewire.user._form_user', [
                'allRoles' => $allRoles,
                'isEditMode' => $isEditMode,
                'existingImageUrl' => $existingImageUrl
            ])
            <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-end">
                    <a href="{{ route('users.index') }}"
                       wire:navigate
                       class="bg-neutral-200 hover:bg-neutral-300 mr-2 text-ba text-neutral-900 px-3 py-1.5 rounded-none shadow-xs inline-flex items-center">
                        <span class="icon-[tabler--x] w-4 h-4 mr-2"></span>
                        Cancelar
                    </a>
                    <x-button type="submit"
                              wire:loading.attr="disabled"
                              wire:target="saveUser"
                              icon="tabler--device-floppy">
                        Salvar
                    </x-button>

                </div>
            </div>
        </div>
    </form>
</div>
