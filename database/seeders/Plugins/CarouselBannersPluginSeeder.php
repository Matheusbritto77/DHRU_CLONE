<?php

namespace Database\Seeders\Plugins;

class CarouselBannersPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'carousel-banners';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Carrossel de Banners',
            'type' => 'hero',
            'is_system' => true,
            'icon' => 'heroicon-o-photo',
            'description' => 'Carrossel rotativo de imagens ponta-a-ponta.',
            'has_admin_panel' => true,
            'admin_panel_location' => 'both',
            'admin_navigation_group' => 'Personalização do Site',
            'admin_navigation_label' => 'Gerenciar banners',
            'admin_navigation_icon' => 'heroicon-o-photo',
            'admin_page_title' => 'Gerenciar banners do carrossel',
            'admin_component_class' => 'App\\Plugins\\CarouselBanners\\Livewire\\ManageCarouselBanners',
            'admin_form_schema' => [
                [
                    'type' => 'image',
                    'name' => 'images',
                    'label' => 'Imagens do carrossel',
                    'helperText' => 'Envie uma ou mais imagens. A ordem enviada será usada no carrossel.',
                    'multiple' => true,
                    'directory' => 'plugin_uploads/carousel-banners',
                    'columnSpan' => 'full',
                ],
                [
                    'type' => 'text',
                    'name' => 'interval_ms',
                    'label' => 'Intervalo entre slides (ms)',
                    'numeric' => true,
                ],
                [
                    'type' => 'text',
                    'name' => 'bg_color',
                    'label' => 'Cor de fundo',
                ],
            ],
            'default_settings' => [
                'images' => ['image/imagem1.png', 'image/imagem2.png', 'image/imagem3.png'],
                'interval_ms' => 4000,
                'bg_color' => '#f5f5f7',
            ],
            'blade_template' => '<section style="background-color: {{ $settings[\'bg_color\'] }};" class="w-full relative overflow-hidden">
    <div class="plugin-carousel-track flex w-full transition-transform duration-700 ease-in-out" data-interval="{{ $settings[\'interval_ms\'] }}">
        @foreach($settings[\'images\'] as $img)
            <img src="{{ asset($img) }}" class="w-full h-auto object-contain flex-shrink-0" alt="Banner">
        @endforeach
    </div>
</section>',
            'js' => 'document.addEventListener("DOMContentLoaded", function() {
    const track = document.querySelector(".plugin-carousel-track");
    if (track) {
        const images = track.querySelectorAll("img");
        let index = 0;
        const interval = Number(track.dataset.interval || 4000);
        if (images.length > 0) {
            setInterval(function() { index = (index + 1) % images.length; track.style.transform = "translateX(-" + (index * 100) + "%)"; }, interval);
        }
    }
});',
        ];
    }
}
