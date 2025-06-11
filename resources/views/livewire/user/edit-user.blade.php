<?php

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component
{
    use WithFileUploads;

    public User $user;

    public string $name = '';
    public string $email = '';
    public string $role = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?string $existingImageUrl = null;

    public array $allRoles = [];
    public bool $isEditMode = true;
    public array $headerItems = [];


    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;

        $this->allRoles = collect(UserRole::cases())->mapWithKeys(function ($role) {
            return [$role->value => Str::title($role->value)];
        })->all();

        $this->headerItems = [
            [
                'label' => 'Usuários | Editar',
                'description'=> 'Manutenção de Usuários do Sistema',
                'icon' => 'tabler--users',
                'iconBtn' => 'icon-park-solid--back',
                'route' => 'users.index',
                'labelBtn' => 'Voltar',
            ]
        ];

    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user->id,
            'role' => ['required', 'string', new \Illuminate\Validation\Rules\Enum(UserRole::class)],
            'password' => ['nullable', 'string', Password::defaults(), 'confirmed'],
        ];
    }

    public function updateUser(): void
    {
        $validatedData = $this->validate();
        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
        ];

        if (!empty($validatedData['password'])) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }


        $this->user->update($updateData);

        session()->flash('success', 'Usuário "' . htmlspecialchars($this->user->name) . '" atualizado com sucesso.');
        $this->redirectRoute('users.index', navigate: true);
    }
}; ?>

<div>
    <x-header-module :items="$headerItems" />

    <form wire:submit="updateUser">
        <div class="bg-white dark:bg-neutral-800 shadow-sm border border-neutral-200 dark:border-neutral-700 rounded-none p-6">
            @include('livewire.user._form_user', [
                'allRoles' => $allRoles,
                'isEditMode' => $isEditMode,
                'existingImageUrl' => $existingImageUrl
            ])
            <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-end">
                    <a href="{{ route('users.index') }}"
                       wire:navigate
                       class="bg-neutral-200 hover:bg-neutral-300 mr-2 text-ba text-neutral-900 px-3 py-1.5 rounded-none shadow-sm inline-flex items-center">
                        <span class="icon-[tabler--x] w-4 h-4 mr-2"></span>
                        Cancelar
                    </a>
                    <x-button type="submit"
                              wire:loading.attr="disabled"
                              wire:target="updateUser"
                              icon="tabler--device-floppy">
                        Salvar
                    </x-button>
                </div>
            </div>
        </div>
    </form>
</div>
