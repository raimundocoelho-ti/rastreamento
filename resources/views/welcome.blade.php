<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sabio System Tecnologia</title> {{-- Título da página atualizado --}}

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- Considere adicionar um fallback ou uma mensagem aqui se os assets não forem encontrados --}}
    @endif
    {{-- Adicione esta linha para incluir os ícones Iconify --}}
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex flex-col min-h-screen">

{{-- Novo Header --}}
<header class="bg-white shadow-sm py-4 px-6 lg:px-8 flex justify-between items-center w-full">
    {{-- Logotipo da aplicação no header --}}
    <div>
        <x-application-logo class="block h-10 w-auto text-gray-800" />
    </div>

    {{-- Botão Entrar --}}
    <div>
        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
            Entrar
            <iconify-icon icon="mdi:login" class="ml-2" width="20" height="20"></iconify-icon>
        </a>
    </div>
</header>

{{-- Conteúdo principal da página --}}
<main class="flex-grow flex flex-col items-center justify-center p-6 lg:p-8">
    <div class="max-w-7xl mx-auto w-full"> {{-- Adicionado w-full para garantir que o div ocupe a largura total --}}

        <div class="p-6 lg:p-8 bg-white border-b border-gray-200 rounded-t-lg"> {{-- Borda superior arredondada --}}
            {{-- Seu logotipo da aplicação (removido daqui, agora no header) --}}
            <div class="text-center">
                {{-- <x-application-logo class="block h-12 w-auto" /> --}}
            </div>

            <h1 class="mt-4 text-4xl font-extrabold text-gray-900 text-center"> {{-- Ajuste no mt para o título --}}
                Sabio System Tecnologia: Sua Frota no Controle Total
            </h1>

            <p class="mt-6 text-gray-700 text-lg leading-relaxed text-center max-w-2xl mx-auto">
                Na Sabio System, somos especialistas em otimizar a gestão e a segurança da sua frota.
                Com soluções avançadas de rastreamento veicular e gerenciamento inteligente,
                transformamos a complexidade em simplicidade para o seu negócio.
            </p>
        </div>

        <div class="bg-gray-100 bg-opacity-75 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 p-6 lg:p-8">

            {{-- Card de Gestão de Frotas --}}
            <div class="p-6 bg-white rounded-lg shadow-md flex flex-col items-center text-center">
                <iconify-icon icon="mdi:car-multiple" width="48" height="48" class="text-indigo-600 mb-4"></iconify-icon>
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">
                    Gestão de Frotas Eficiente
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    Otimize rotas, reduza custos com combustível e manutenção, e aumente a produtividade da sua operação.
                    Tenha uma visão completa e em tempo real de cada veículo.
                </p>
            </div>

            {{-- Card de Rastreamento Veicular --}}
            <div class="p-6 bg-white rounded-lg shadow-md flex flex-col items-center text-center">
                <iconify-icon icon="mdi:map-marker-path" width="48" height="48" class="text-green-600 mb-4"></iconify-icon>
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">
                    Rastreamento Veicular Preciso
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    Monitore a localização exata dos seus veículos, receba alertas inteligentes e garanta a segurança dos seus ativos e motoristas.
                </p>
            </div>

            {{-- Card de Segurança e Conformidade --}}
            <div class="p-6 bg-white rounded-lg shadow-md flex flex-col items-center text-center">
                <iconify-icon icon="mdi:security" width="48" height="48" class="text-red-600 mb-4"></iconify-icon>
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">
                    Segurança e Conformidade
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    Com nossa tecnologia, você garante a conformidade com regulamentações e eleva o nível de segurança em todas as suas operações.
                </p>
            </div>
        </div>

        <div class="p-6 lg:p-8 bg-white border-t border-gray-200 rounded-b-lg text-center"> {{-- Borda inferior arredondada --}}
            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                Pronto para Transformar sua Gestão de Frota?
            </h2>
            <p class="text-gray-700 text-lg mb-6">
                Entre em contato conosco e descubra como a Sabio System pode impulsionar o seu negócio.
            </p>
            <a href="#" class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Fale Conosco Agora
                <iconify-icon icon="mdi:arrow-right" class="ml-2" width="20" height="20"></iconify-icon>
            </a>
        </div>
    </div>
</main>

</body>
</html>
