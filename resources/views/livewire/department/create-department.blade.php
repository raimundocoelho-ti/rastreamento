<?php

use App\Models\Department;
use App\Models\User;

// Para popular o select de gestores
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    #[Validate('required|string|max:255|unique:departments,name')]
    public string $name = '';

    #[Validate('nullable|integer|exists:users,id')] // manager_id pode ser nulo
    public string $manager_id = ''; // Usar string para o valor "" do select

    #[Validate('nullable|string|max:1000')] // Limitar tamanho da descrição
    public string $description = '';

    #[Validate('nullable|image|max:2048')] // Max 2MB para a imagem
    public $image; // Para o upload de imagem

    // Propriedades para popular o formulário
    public array $managers = [];
    public ?string $existingImageUrl = null; // Para consistência com o _form_department, mas não usado na criação
    public bool $isEditMode = false;     // Para consistência com o _form_department
    public array $headerItems = [];

    public function mount(): void
    {
        // Carrega todos os utilizadores para o select de gestores.
        // Poderia ser filtrado por papel se necessário (ex: apenas 'manager' ou 'admin')
        $this->managers = User::orderBy('name')->get(['id', 'name', 'email'])->toArray();

        $this->headerItems = [
            [
                'label' => 'Departamento | Cadastrar',
                'description' => 'Manutenção de Departamentos do Sistema',
                'icon' => 'tabler--building-community',
                'iconBtn' => 'ion--caret-back',
                'route' => 'departments.index',
                'labelBtn' => 'Voltar',
            ]
        ];
    }

    public function removeNewImage()
    {
        $this->image = null;
        $this->resetValidation('image'); // Limpa erros de validação para o campo imagem
    }

    public function saveDepartment(): void
    {
        $this->validate();

        $imagePathForDb = null;
        if ($this->image) {
            $imageName = Str::uuid() . '.' . $this->image->getClientOriginalExtension();
            // Caminho relativo ao disco 'public' onde as imagens de departamento serão guardadas
            $imagePathForDb = 'department_images/' . $imageName;

            // Diretório de destino absoluto
            $targetDirectory = storage_path('app/public/department_images');

            // Criar o diretório se não existir
            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0775, true);
            }

            // Processar e guardar a imagem
            $img = InterventionImage::read($this->image->getRealPath());
            $img->cover(300, 300); // Exemplo: redimensionar para 300x300px. Ajuste conforme necessário.
            $img->save(storage_path('app/public/' . $imagePathForDb));
        }

        Department::create([
            'name' => $this->name,
            'manager_id' => $this->manager_id ?: null, // Garante que é null se for uma string vazia
            'description' => $this->description,
            'image' => $imagePathForDb,
        ]);

        session()->flash('success', 'Departamento "' . htmlspecialchars($this->name) . '" criado com sucesso.');
        $this->redirectRoute('departments.index', navigate: true);
    }

    // Para passar a lista de roles (neste caso, managers) para o _form_department
    // O _form_department espera $roles, mas aqui temos $managers.
    // Para manter o _form_department genérico, ele itera sobre $roles.
    // No entanto, o nosso _form_department para departamentos foi feito para iterar sobre $managers.
    // Se quiséssemos um _form_department ainda mais genérico, poderíamos passar 'selectOptions' => $this->managers.
    // Mas como já fizemos o _form_department específico, ele espera $managers.
    public function rendering(\Illuminate\View\View $view): void
    {
        // Esta abordagem não é necessária se o _form_department já usa $managers diretamente.
        // $view->with('roles', $this->managers); // Exemplo se o form usasse $roles para managers
    }

}; ?>

<div>
    <x-header-module :items="$headerItems"/>

    <form wire:submit="saveDepartment">
        <div
            class="bg-white dark:bg-neutral-800 shadow-sm border border-neutral-200 dark:border-neutral-700 rounded-none p-6">
            {{-- Passar a variável $managers para o formulário parcial --}}
            {{-- As outras propriedades públicas ($name, $description, $image, $existingImageUrl, $isEditMode)
                 são automaticamente acessíveis dentro do @include se o form as utilizar. --}}
            @include('livewire.department._form_department', [
                'managers' => $managers,
                'roles' => [], /* Para o _form_user, não usado pelo _form_department */
                'isEditMode' => $isEditMode, /* Para o _form_user, pode não ser usado pelo _form_department */
                'existingImageUrl' => $existingImageUrl /* Para o _form_user e _form_department */
            ])

            <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-end">
                    <a href="{{ route('departments.index') }}"
                       wire:navigate
                       class="bg-neutral-200 hover:bg-neutral-300 mr-2 text-ba text-neutral-900 px-3 py-1.5 rounded-none shadow-sm inline-flex items-center">
                        <span class="icon-[tabler--x] w-4 h-4 mr-2"></span>
                        Cancelar
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveDepartment"
                            class="bg-neutral-900 hover:bg-neutral-700 text-white font-medium text-sm py-1.5 px-3 rounded-none shadow-sm disabled:opacity-75 inline-flex items-center">
                        <span wire:loading wire:target="saveDepartment"
                              class="icon-[tabler--loader-2] animate-spin w-4 h-4 mr-2"></span>
                        <span wire:loading wire:target="saveDepartment">A salvar...</span>
                        <span wire:loading.remove wire:target="saveDepartment">
        <span class="icon-[tabler--device-floppy] w-4 h-4 mr-2"></span>
        Salvar
    </span>
                    </button>


                </div>
            </div>
        </div>
    </form>
</div>
