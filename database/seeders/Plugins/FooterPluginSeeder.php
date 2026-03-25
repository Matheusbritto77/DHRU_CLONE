<?php

namespace Database\Seeders\Plugins;

class FooterPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'footer';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Rodapé do Site',
            'type' => 'footer',
            'is_system' => true,
            'icon' => 'heroicon-o-bars-3-bottom-left',
            'description' => 'Rodapé completo com logo, redes sociais e direitos reservados.',
            'default_settings' => [
                'logo_src' => 'logo3.jpg',
                'description' => 'Soluções inovadoras e suporte focado no que importa. Construído com sofisticação.',
                'email' => 'contato@brserver.tech',
                'whatsapp' => '+55 (34) 99944-2627',
                'whatsapp_link' => 'https://wa.me/5534999442627',
                'copyright' => 'BR Server. Todos os direitos reservados.',
            ],
            'blade_template' => '<footer class="bg-white border-t border-gray-200 pt-16 pb-8 px-4">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 mb-12">
        <div>
            <img src="{{ asset($settings[\'logo_src\']) }}" class="h-8 w-auto mb-6 rounded" alt="Logo">
            <p class="text-gray-500 text-sm font-light max-w-xs">{{ $settings[\'description\'] }}</p>
        </div>
        <div>
            <h4 class="font-semibold text-[#1d1d1f] mb-4 text-sm">Siga-nos</h4>
            <div class="flex space-x-3">
                <a href="{{ $settings[\'whatsapp_link\'] }}" class="w-9 h-9 rounded-full bg-[#f5f5f7] flex items-center justify-center text-gray-600 hover:bg-[#0066cc] hover:text-white transition group"><i class="fab fa-whatsapp group-hover:scale-110 transition-transform"></i></a>
                <a href="#" class="w-9 h-9 rounded-full bg-[#f5f5f7] flex items-center justify-center text-gray-600 hover:bg-[#0066cc] hover:text-white transition group"><i class="fab fa-instagram group-hover:scale-110 transition-transform"></i></a>
                <a href="#" class="w-9 h-9 rounded-full bg-[#f5f5f7] flex items-center justify-center text-gray-600 hover:bg-[#0066cc] hover:text-white transition group"><i class="fab fa-telegram group-hover:scale-110 transition-transform"></i></a>
            </div>
        </div>
        <div>
            <h4 class="font-semibold text-[#1d1d1f] mb-4 text-sm">Contatos</h4>
            <ul class="space-y-2 text-gray-500 text-sm font-light">
                <li><a href="mailto:{{ $settings[\'email\'] }}" class="hover:text-[#0066cc] transition">{{ $settings[\'email\'] }}</a></li>
                <li><a href="{{ $settings[\'whatsapp_link\'] }}" class="hover:text-[#0066cc] transition">{{ $settings[\'whatsapp\'] }}</a></li>
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto border-t border-gray-100 pt-8 flex justify-between items-center text-xs text-gray-400 font-light">
        <p>&copy; <?php echo date("Y"); ?> {{ $settings[\'copyright\'] }}</p>
    </div>
</footer>',
        ];
    }
}
