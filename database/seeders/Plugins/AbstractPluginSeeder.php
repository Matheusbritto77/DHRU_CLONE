<?php

namespace Database\Seeders\Plugins;

use App\Models\Plugin;
use Illuminate\Database\Seeder;

abstract class AbstractPluginSeeder extends Seeder
{
    final public function run(): void
    {
        $plugin = Plugin::updateOrCreate(
            ['slug' => $this->slug()],
            array_replace([
                'version' => '1.0.0',
                'is_system' => false,
                'is_active' => true,
                'has_admin_panel' => false,
                'admin_panel_location' => 'admin',
                'blade_template' => '<div></div>',
            ], $this->definition())
        );

        $this->afterUpsert($plugin);
    }

    abstract protected function slug(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function definition(): array;

    protected function afterUpsert(Plugin $plugin): void
    {
    }
}
