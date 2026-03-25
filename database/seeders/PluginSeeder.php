<?php

namespace Database\Seeders;

use Database\Seeders\Pages\AddCreditsPageSeeder;
use Database\Seeders\Pages\LoginPageSeeder;
use Database\Seeders\Pages\DashboardPageSeeder;
use Database\Seeders\Pages\ImeiHistoryPageSeeder;
use Database\Seeders\Pages\ImeiServicesPageSeeder;
use Database\Seeders\Pages\RegisterPageSeeder;
use Database\Seeders\Pages\ServerHistoryPageSeeder;
use Database\Seeders\Pages\ServerServicesPageSeeder;
use Database\Seeders\Pages\WelcomePageSeeder;
use Database\Seeders\Plugins\AddCreditsFormPluginSeeder;
use Database\Seeders\Plugins\AddCreditsHelpPluginSeeder;
use Database\Seeders\Plugins\AddCreditsHeroPluginSeeder;
use Database\Seeders\Plugins\AuthLoginFormPluginSeeder;
use Database\Seeders\Plugins\AuthLoginHeaderPluginSeeder;
use Database\Seeders\Plugins\AuthLoginSupportPluginSeeder;
use Database\Seeders\Plugins\AuthRegisterFormPluginSeeder;
use Database\Seeders\Plugins\AuthRegisterHeaderPluginSeeder;
use Database\Seeders\Plugins\AuthRegisterSupportPluginSeeder;
use Database\Seeders\Plugins\CarouselBannersPluginSeeder;
use Database\Seeders\Plugins\DashboardHeroPluginSeeder;
use Database\Seeders\Plugins\DashboardQuickLinksPluginSeeder;
use Database\Seeders\Plugins\DashboardSidebarPluginSeeder;
use Database\Seeders\Plugins\DashboardStatsPluginSeeder;
use Database\Seeders\Plugins\FeaturesGridPluginSeeder;
use Database\Seeders\Plugins\FooterPluginSeeder;
use Database\Seeders\Plugins\HeroTextPluginSeeder;
use Database\Seeders\Plugins\ImeiServicesHelpPluginSeeder;
use Database\Seeders\Plugins\ImeiServicesHeroPluginSeeder;
use Database\Seeders\Plugins\ImeiServicesTablePluginSeeder;
use Database\Seeders\Plugins\ImeiHistoryHelpPluginSeeder;
use Database\Seeders\Plugins\ImeiHistoryHeroPluginSeeder;
use Database\Seeders\Plugins\ImeiHistoryTablePluginSeeder;
use Database\Seeders\Plugins\NavbarPluginSeeder;
use Database\Seeders\Plugins\BinancePayGatewayPluginSeeder;
use Database\Seeders\Plugins\GerencianetPixGatewayPluginSeeder;
use Database\Seeders\Plugins\MercadoPagoGatewayPluginSeeder;
use Database\Seeders\Plugins\ServerHistoryHelpPluginSeeder;
use Database\Seeders\Plugins\ServerHistoryHeroPluginSeeder;
use Database\Seeders\Plugins\ServerHistoryTablePluginSeeder;
use Database\Seeders\Plugins\ServerServicesHelpPluginSeeder;
use Database\Seeders\Plugins\ServerServicesHeroPluginSeeder;
use Database\Seeders\Plugins\ServerServicesTablePluginSeeder;
use Database\Seeders\Plugins\StripeGatewayPluginSeeder;
use Database\Seeders\Plugins\TopbarPluginSeeder;
use Illuminate\Database\Seeder;

class PluginSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TopbarPluginSeeder::class,
            NavbarPluginSeeder::class,
            CarouselBannersPluginSeeder::class,
            HeroTextPluginSeeder::class,
            FeaturesGridPluginSeeder::class,
            FooterPluginSeeder::class,
            DashboardSidebarPluginSeeder::class,
            DashboardHeroPluginSeeder::class,
            DashboardStatsPluginSeeder::class,
            DashboardQuickLinksPluginSeeder::class,
            AddCreditsHeroPluginSeeder::class,
            AddCreditsFormPluginSeeder::class,
            AddCreditsHelpPluginSeeder::class,
            ImeiServicesHeroPluginSeeder::class,
            ImeiServicesTablePluginSeeder::class,
            ImeiServicesHelpPluginSeeder::class,
            ImeiHistoryHeroPluginSeeder::class,
            ImeiHistoryTablePluginSeeder::class,
            ImeiHistoryHelpPluginSeeder::class,
            ServerServicesHeroPluginSeeder::class,
            ServerServicesTablePluginSeeder::class,
            ServerServicesHelpPluginSeeder::class,
            ServerHistoryHeroPluginSeeder::class,
            ServerHistoryTablePluginSeeder::class,
            ServerHistoryHelpPluginSeeder::class,
            AuthLoginHeaderPluginSeeder::class,
            AuthLoginFormPluginSeeder::class,
            AuthLoginSupportPluginSeeder::class,
            AuthRegisterHeaderPluginSeeder::class,
            AuthRegisterFormPluginSeeder::class,
            AuthRegisterSupportPluginSeeder::class,
            GerencianetPixGatewayPluginSeeder::class,
            BinancePayGatewayPluginSeeder::class,
            MercadoPagoGatewayPluginSeeder::class,
            StripeGatewayPluginSeeder::class,
            DarkModeSeeder::class,
            WelcomePageSeeder::class,
            DashboardPageSeeder::class,
            AddCreditsPageSeeder::class,
            ImeiServicesPageSeeder::class,
            ImeiHistoryPageSeeder::class,
            ServerServicesPageSeeder::class,
            ServerHistoryPageSeeder::class,
            LoginPageSeeder::class,
            RegisterPageSeeder::class,
        ]);
    }
}
