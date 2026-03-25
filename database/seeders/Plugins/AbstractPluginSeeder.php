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
            $this->definition()
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
