<?php

namespace Database\Seeders\Plugins;

class FeaturesGridPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'features-grid';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Cartões de Funcionalidades',
            'type' => 'section',
            'is_system' => true,
            'icon' => 'heroicon-o-squares-2x2',
            'description' => 'Grade de 3 cards destacando serviços e diferenciais.',
            'default_settings' => [
                'section_title' => 'Serviços pensados para você.',
                'section_subtitle' => 'A tecnologia mais avançada, focada em detalhes.',
                'cards' => [
                    ['icon' => 'fas fa-layer-group', 'icon_bg' => '#dbeafe', 'icon_color' => '#0066cc', 'title' => 'AUTO API', 'text' => 'Serviços incríveis com API automática disponível 24 horas. Rapidez e eficiência para a sua aplicação com máxima estabilidade.'],
                    ['icon' => 'fas fa-headset', 'icon_bg' => '#d1fae5', 'icon_color' => '#059669', 'title' => 'Suporte 24/7', 'text' => 'Estamos prontos para atender você com um atendimento ágil e personalizado, a qualquer hora.'],
                    ['icon' => 'fas fa-tag', 'icon_bg' => '#ede9fe', 'icon_color' => '#7c3aed', 'title' => 'Revendedores', 'text' => 'Condições exclusivas e descontos competitivos. Maximize seus ganhos conosco.'],
                ],
            ],
            'blade_template' => '<section class="py-24 bg-white px-4">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 fade-in-up">
            <h2 class="text-4xl font-semibold tracking-tight text-[#1d1d1f]">{{ $settings[\'section_title\'] }}</h2>
            <p class="mt-4 text-xl text-gray-500 font-light">{{ $settings[\'section_subtitle\'] }}</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($settings[\'cards\'] as $card)
            <div class="bg-[#f5f5f7] border border-gray-100 rounded-3xl p-10 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 fade-in-up">
                <div style="background-color: {{ $card[\'icon_bg\'] }}; color: {{ $card[\'icon_color\'] }};" class="w-12 h-12 rounded-full flex items-center justify-center text-xl mb-6 shadow-sm"><i class="{{ $card[\'icon\'] }}"></i></div>
                <h3 class="text-2xl font-semibold text-[#1d1d1f] mb-3 tracking-tight">{{ $card[\'title\'] }}</h3>
                <p class="text-gray-500 leading-relaxed text-[16px] font-light">{{ $card[\'text\'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>',
        ];
    }
}
