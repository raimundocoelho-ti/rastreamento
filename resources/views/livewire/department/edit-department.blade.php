<?php

use App\Models\Department;
use App\Models\User; // Para popular o select de gestores
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
// Não usaremos #[Validate] nas propriedades, mas sim um método rules()

new #[Layout('layouts.app')] class extends Component
{
    use WithFileUploads;

    public Department $department; // Injeção do modelo através de Route Model Binding

    // Propriedades para o formulário
    public string $name = '';
    public string $manager_id = ''; // Usar string para o valor "" do select
    public string $description = '';
    public $image; // Para o novo upload de imagem
    public ?string $existingImageUrl = null; // Para exibir a imagem atual

    // Propriedades de suporte
    public array $managers = [];
    public bool $isEditMode = true;
    public array $headerItems = [];

    // Método para definir as regras de validação
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:departments,name,' . $this->department->id,
            'manager_id' => 'nullable|integer|exists:users,id',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ];
    }

    // Mensagens de validação personalizadas (opcional)
    protected function messages(): array
    {
        return [
            'name.required' => 'O nome do departamento é obrigatório.',
            'name.unique' => 'Este nome de departamento já existe.',
            'manager_id.exists' => 'O gestor selecionado não é válido.',
            'image.image' => 'O ficheiro deve ser uma imagem.',
            'image.max' => 'A imagem não pode ter mais de 2MB.',
        ];
    }

    public function mount(Department $department): void
    {
        $this->department = $department; // Atribui o departamento carregado
        $this->name = $department->name;
        $this->manager_id = (string) $department->manager_id; // Converte para string para o select
        $this->description = $department->description ?? ''; // Garante que não é null
        $this->existingImageUrl = $department->image_url; // Usa o accesor getImageUrlAttribute()

        $this->managers = User::orderBy('name')->get(['id', 'name', 'email'])->toArray();

        $this->headerItems = [
            [
                'label' => 'Departamento | Editar',
                'description'=> 'Manutenção de Departamentos do Sistema',
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
        $this->resetValidation('image');
    }

    public function updateDepartment(): void
    {
        $validatedData = $this->validate(); // Usa o método rules() definido acima

        $updateData = [
            'name' => $validatedData['name'],
            'manager_id' => $validatedData['manager_id'] ?: null,
            'description' => $validatedData['description'],
        ];

        if ($this->image) { // Se uma nova imagem foi carregada
            // Apagar a imagem antiga, se existir
            if ($this->department->image) {
                Storage::disk('public')->delete($this->department->image);
                Log::info('Imagem antiga do departamento apagada: ' . $this->department->image);
            }

            // Processar e guardar a nova imagem
            $imageName = Str::uuid() . '.' . $this->image->getClientOriginalExtension();
            $imagePathForDb = 'department_images/' . $imageName;
            $targetDirectory = storage_path('app/public/department_images');

            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0775, true);
            }

            $img = InterventionImage::read($this->image->getRealPath());
            $img->cover(300, 300); // Ajuste o tamanho conforme necessário
            $img->save(storage_path('app/public/' . $imagePathForDb));

            $updateData['image'] = $imagePathForDb;
            Log::info('Nova imagem do departamento guardada: ' . $imagePathForDb);
        }
        // Se não houver nova imagem ($this->image é null), o campo 'image' não é incluído em $updateData,
        // então a imagem existente no department->image permanece inalterada.
        // Se quiseres uma opção para *remover* a imagem existente, precisarias de lógica adicional.

        $this->department->update($updateData);

        session()->flash('success', 'Departamento "' . htmlspecialchars($this->department->name) . '" atualizado com sucesso.');
        $this->redirectRoute('departments.index', navigate: true);
    }
}; ?>

<div>
    <x-header-module :items="$headerItems" />

    <form wire:submit="updateDepartment">
        <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-none p-6">
            {{-- As propriedades públicas do componente ($name, $manager_id, $description, $image, $existingImageUrl, $managers, $isEditMode)
                 são automaticamente acessíveis dentro do @include se o form as utilizar. --}}
            @include('livewire.department._form_department', [
                'managers' => $managers,
                'roles' => [], /* Para o _form_user, não usado pelo _form_department */
                'isEditMode' => $isEditMode, /* Para o _form_department, $isEditMode do componente pai é usado */
                'existingImageUrl' => $existingImageUrl /* Para o _form_department, $existingImageUrl do componente pai é usado */
            ])

            <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-end">

                    <a href="{{ route('departments.index') }}"
                       wire:navigate
                       class="bg-neutral-200 hover:bg-neutral-300 mr-2 text-ba text-neutral-900 px-3 py-1.5 rounded-none shadow-xs inline-flex items-center">
                        <span class="icon-[tabler--x] w-4 h-4 mr-2"></span>
                        Cancelar
                    </a>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveDepartment"
                            class="bg-neutral-900 hover:bg-neutral-700 text-white text-sm font-medium py-1.5 px-3 rounded-none shadow-xs disabled:opacity-75 inline-flex items-center">
                        <span wire:loading wire:target="saveDepartment" class="icon-[tabler--loader-2] animate-spin w-4 h-4 mr-2"></span>
                        <span wire:loading wire:target="saveDepartment">A salvar...</span>
                        <span wire:loading.remove wire:target="saveDepartment">
        <span class="icon-[tabler--device-floppy] w-4 h-4 mr-2"></span>Salvar
    </span>
                    </button>

                </div>
            </div>
        </div>
    </form>
</div>
