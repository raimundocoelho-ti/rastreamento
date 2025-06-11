<?php

// Removidas as importações de modelos de Vehicle, VehicleLocation, Brand, etc.
// pois não serão usados para buscar dados reais nesta fase de teste
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url; // Para manter o vehicleId na URL (se um dia for útil)
use Livewire\Attributes\On; // Para os listeners de eventos Livewire
use Livewire\Attributes\Computed; // Para propriedades computadas, se as usar futuramente

new #[Layout('layouts.app')] class extends Component
{
    // Propriedade para o ID do veículo, que pode vir da rota, mas não será usada para buscar dados reais agora
    #[Url(as: 'v', history: true, keep: true)]
    public int $vehicleId;

    // Propriedade para itens do cabeçalho da página
    public array $headerItems = [];

    // Propriedade computada para simular o veículo, com dados hardcoded
    #[Computed]
    public function vehicle(): object
    {
        // Simulando um objeto de veículo com dados básicos
        return (object)[
            'id' => $this->vehicleId,
            'license_plate' => 'TESTE-001',
            'vehicleModel' => (object)['name' => 'Modelo Teste', 'brand' => (object)['name' => 'Marca Teste']],
            'vehicleType' => (object)['name' => 'Tipo Teste'],
            'color' => 'Branco',
        ];
    }

    // Propriedade para simular a última localização, com dados hardcoded
    public ?array $latestLocationData = null; // Renomeado para evitar conflito com model

    // Dados simulados de localização, podem ser alterados para testar diferentes pontos
    private array $simulatedLocations = [
        'default' => [
            'latitude' => -23.55052, // São Paulo, Brasil
            'longitude' => -46.63330,
            'updated_at' => '2025-06-10 10:00:00',
            'speed' => 50.0,
            'heading' => 45,
            'altitude' => 750.0,
            'tracking_session_id' => 'SIMULATED_SESSION_1',
        ],
        // Você pode adicionar mais localizações para simular diferentes veículos ou pontos
        1 => [ // Exemplo de um ID de veículo específico
            'latitude' => -22.9068, // Rio de Janeiro, Brasil
            'longitude' => -43.1729,
            'updated_at' => '2025-06-10 11:00:00',
            'speed' => 80.0,
            'heading' => 270,
            'altitude' => 20.0,
            'tracking_session_id' => 'SIMULATED_SESSION_2',
        ],
        // Adicione outros IDs de veículos conforme a necessidade do seu teste
        26 => [ // Se você estiver testando com o ID 26
            'latitude' => -23.5877, // Outro ponto em SP
            'longitude' => -46.6535,
            'updated_at' => '2025-06-10 12:00:00',
            'speed' => 25.0,
            'heading' => 180,
            'altitude' => 780.0,
            'tracking_session_id' => 'SIMULATED_SESSION_3',
        ],
    ];

    /**
     * Método de inicialização do componente.
     */
    public function mount(int $vehicleId): void
    {
        $this->vehicleId = $vehicleId;
        $this->loadSimulatedLocation(); // Carrega a localização simulada

        $this->headerItems = [
            [
                'label' => 'Rastreamento de Veículo (Teste)',
                'description' => 'Localização simulada do veículo: ' . ($this->vehicle->license_plate),
                'icon' => 'tabler--map-pin',
                'route' => 'locations.index',
                'labelBtn' => 'Voltar para Listagem',
                'iconBtn' => 'tabler--arrow-left',
            ]
        ];
    }

    /**
     * Carrega a localização simulada baseada no vehicleId.
     */
    public function loadSimulatedLocation(): void
    {
        // Tenta pegar a localização pelo ID do veículo, se não encontrar, usa a 'default'
        $location = $this->simulatedLocations[$this->vehicleId] ?? $this->simulatedLocations['default'];

        $this->latestLocationData = $location;

        // Dispara um evento Livewire para o JS com os dados simulados
        $this->dispatch('locationUpdated', [
            'vehicleId' => $this->vehicleId,
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'vehicleName' => $this->vehicle->license_plate, // Usar a placa simulada
        ]);
    }

    /**
     * Reage a mudanças na propriedade $vehicleId (seletor).
     */
    public function updatedVehicleId(): void
    {
        $this->loadSimulatedLocation(); // Recarrega a localização simulada

        // Atualiza a descrição do cabeçalho
        $this->headerItems[0]['description'] = 'Localização simulada do veículo: ' . ($this->vehicle->license_plate);

        // Dispara evento para o JS re-renderizar o mapa (já feito em loadSimulatedLocation)
        // Você pode disparar outro evento aqui se a lógica no JS for diferente para atualização via dropdown
    }

    /**
     * Renderiza a view.
     */
    public function with(): array
    {
        // allVehiclesForDropdown não é necessário para este teste simples
        // 'vehicle' e 'latestLocationData' são acessíveis via $this->
        return [
            'headerItems' => $this->headerItems,
        ];
    }
}; ?>

{{-- HTML da View Blade --}}
<div>
    <x-header-module :items="$headerItems" />

    {{-- Mensagens (não serão exibidas neste teste, mas mantidas por compatibilidade) --}}
    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded-md relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded-md relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Bloco do Seletor de Veículos (Populando com IDs simulados) --}}
    <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-md p-3 mb-6">
        <label for="selectVehicle" class="sr-only">Selecionar Veículo</label>
        <div class="relative">
            <select wire:model.live="vehicleId" id="selectVehicle"
                    class="block w-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-neutral-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 sm:text-sm rounded-md py-2 pl-3 pr-8 appearance-none">
                <option value="default">Veículo Padrão (SP)</option>
                <option value="1">Veículo 1 (RJ)</option>
                <option value="26">Veículo 26 (SP)</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-neutral-700 dark:text-neutral-300">
                <span class="icon-[tabler--selector] text-lg"></span>
            </div>
        </div>
    </div>

    {{-- Contêiner do Mapa e Detalhes da Localização --}}
    <div class="bg-white dark:bg-neutral-800 shadow-xs border border-neutral-200 dark:border-neutral-700 rounded-md p-3 mb-6">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Localização Atual</h2>
        <div id="map" style="height: 500px; width: 100%; border-radius: 0.375rem;"></div>

        @if ($latestLocationData)
            <div class="mt-4 text-sm text-neutral-700 dark:text-neutral-300">
                <p><strong>Latitude:</strong> {{ number_format($latestLocationData['latitude'], 6) }}</p>
                <p><strong>Longitude:</strong> {{ number_format($latestLocationData['longitude'], 6) }}</p>
                <p><strong>Última Atualização:</strong> {{ \Carbon\Carbon::parse($latestLocationData['updated_at'])->translatedFormat('d/m/Y H:i:s') }}</p>
                <p><strong>Velocidade:</strong> {{ $latestLocationData['speed'] ?? 'N/D' }} km/h</p>
                <p><strong>Direção:</strong> {{ $latestLocationData['heading'] ?? 'N/D' }}°</p>
                <p><strong>Altitude:</strong> {{ $latestLocationData['altitude'] ?? 'N/D' }} m</p>
                <p><strong>Sessão de Rastreamento:</strong> {{ $latestLocationData['tracking_session_id'] ?? 'N/D' }}</p>
            </div>
        @else
            <div class="mt-4 text-sm text-neutral-500 dark:text-neutral-400 text-center">
                Nenhuma localização simulada encontrada para este veículo.
            </div>
        @endif
    </div>
</div>

{{-- BLOCO PARA PUSH DOS SCRIPTS LEAFLET E MAPA --}}
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Variáveis globais para o mapa e marcador
        // Não use 'let' aqui se o Livewire/Volt pode re-executar este script globalmente
        map = null;
        marker = null;

        // Função para inicializar o mapa
        function initializeMap(latitude, longitude, zoomLevel = 13) {
            const mapElement = document.getElementById('map');
            if (!mapElement) {
                console.error("Elemento '#map' não encontrado no DOM. O mapa não pode ser inicializado.");
                return;
            }

            if (map) { // Se o mapa já existe, destrua-o para reinicializar
                map.remove();
                map = null; // Garante que a variável map seja resetada
            }

            if (latitude === null || longitude === null || isNaN(latitude) || isNaN(longitude)) {
                console.warn('Coordenadas inválidas para inicializar o mapa. Usando valores padrão (0,0).');
                latitude = 0;
                longitude = 0;
                zoomLevel = 2;
            }
            map = L.map('map').setView([latitude, longitude], zoomLevel);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
        }

        // Função para adicionar ou mover o marcador
        function updateMarker(latitude, longitude, vehicleName) {
            if (latitude === null || longitude === null || isNaN(latitude) || isNaN(longitude)) {
                console.warn('Coordenadas inválidas para atualizar o marcador. Marcador não adicionado/atualizado.');
                if (marker) { map.removeLayer(marker); marker = null; }
                return;
            }

            const iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png';
            const iconRetinaUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png';
            const shadowUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png';

            const customIcon = L.icon({
                iconRetinaUrl: iconRetinaUrl,
                iconUrl: iconUrl,
                shadowUrl: shadowUrl,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                tooltipAnchor: [16, -28],
                shadowSize: [41, 41]
            });

            const latLng = [latitude, longitude];

            if (marker) {
                marker.setLatLng(latLng);
                marker.bindPopup(`<b>${vehicleName}</b><br>Lat: ${latitude.toFixed(6)}<br>Lng: ${longitude.toFixed(6)}`).openPopup();
            } else {
                marker = L.marker(latLng, { icon: customIcon }).addTo(map);
                marker.bindPopup(`<b>${vehicleName}</b><br>Lat: ${latitude.toFixed(6)}<br>Lng: ${longitude.toFixed(6)}`).openPopup();
            }
            map.setView(latLng, map.getZoom() > 5 ? map.getZoom() : 13);
        }

        // Listener para o evento 'locationUpdated' disparado pelo PHP do Livewire
        Livewire.on('locationUpdated', ({ vehicleId, latitude, longitude, vehicleName }) => {
            console.log('Evento locationUpdated recebido:', { vehicleId, latitude, longitude, vehicleName });
            if (latitude !== null && longitude !== null) {
                initializeMap(latitude, longitude);
                updateMarker(latitude, longitude, vehicleName);
            } else {
                initializeMap(0, 0, 2); // Centraliza em 0,0 com zoom baixo
                if (marker) { map.removeLayer(marker); marker = null; }
                console.log('Nenhuma localização válida encontrada para o veículo ID: ' + vehicleId);
            }
        });

        // Listener para o evento 'vehicleSelected' disparado pelo PHP do Livewire (ao mudar o dropdown)
        Livewire.on('vehicleSelected', ({ vehicleId, latitude, longitude, vehicleName }) => {
            console.log('Evento vehicleSelected recebido:', { vehicleId, latitude, longitude, vehicleName });
            if (latitude !== null && longitude !== null) {
                // Se o mapa ainda não foi inicializado (primeira carga da página), inicializa.
                // Caso contrário, apenas move o marcador.
                if (!map) {
                    initializeMap(latitude, longitude);
                }
                updateMarker(latitude, longitude, vehicleName);
            } else {
                initializeMap(0, 0, 2);
                if (marker) { map.removeLayer(marker); marker = null; }
                console.log('Nenhuma localização válida encontrada para o veículo ID: ' + vehicleId + ' após seleção.');
            }
        });

        // Este código será executado quando o script é carregado (após Livewire e Leaflet)
        // Para garantir que o mapa seja inicializado mesmo que o Livewire não dispare um evento inicial imediatamente
        // (Embora o mount() já dispare 'locationUpdated', isso é uma segurança)
        document.addEventListener('livewire:navigated', () => {
            // Este evento dispara após a navegação do Livewire, garantindo que o DOM está pronto.
            // O evento 'locationUpdated' do mount() já deve ter sido enviado.
            // Se o mapa não apareceu, pode ser que o Livewire não esteja enviando o evento.
            // Então, podemos forçar a inicialização aqui, pegando os dados atuais do Livewire
            // ou disparando o método PHP.
            // Para este teste, o 'locationUpdated' já é disparado no mount().
            // Se precisar de um fallback: Livewire.first().call('loadSimulatedLocation');
        });

    </script>
@endpush
