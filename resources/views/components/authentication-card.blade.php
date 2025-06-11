<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="text-center">
        <img src="{{ asset('images/logo-tipo-sabio-system.png') }}" alt="Logo Sábio System"
            {{ $attributes->merge(['class' => 'h-16 w-auto mx-auto mb-4']) }}>

        <h1 class="text-2xl font-bold text-gray-800">Sábio System Tecnologia</h1>
        <p class="text-sm text-gray-500">Sistema de rastreamento veicular e gestão de frotas</p>
    </div>

    <div class="w-full max-w-md mt-8 bg-white p-6 rounded-2xl shadow-lg">
        {{ $slot }}
    </div>
</div>

