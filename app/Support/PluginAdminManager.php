<?php

namespace App\Support;

use App\Models\Plugin;
use Illuminate\Support\Arr;

class PluginAdminManager
{
    public static function getFormData(Plugin $plugin): array
    {
        $stored = $plugin->adminSetting?->settings ?? [];

        return self::denormalizeForForm(
            $plugin,
            array_replace_recursive($plugin->default_settings ?? [], $stored)
        );
    }

    public static function save(Plugin $plugin, array $data): void
    {
        $normalized = self::normalizeForStorage($plugin, $data);

        $plugin->adminSetting()->updateOrCreate(
            ['plugin_id' => $plugin->id],
            ['settings' => $normalized]
        );

        $plugin->forceFill([
            'default_settings' => array_replace_recursive($plugin->default_settings ?? [], $normalized),
        ])->save();
    }

    protected static function normalizeForStorage(Plugin $plugin, array $data): array
    {
        foreach ($plugin->admin_form_schema ?? [] as $field) {
            $name = Arr::get($field, 'name');
            $type = Arr::get($field, 'type');
            $disk = Arr::get($field, 'disk', 'public');

            if (! $name || ! array_key_exists($name, $data)) {
                continue;
            }

            if (! in_array($type, ['file', 'image'], true) || $disk !== 'public') {
                continue;
            }

            $data[$name] = self::prefixPublicPath($data[$name]);
        }

        return $data;
    }

    protected static function denormalizeForForm(Plugin $plugin, array $data): array
    {
        foreach ($plugin->admin_form_schema ?? [] as $field) {
            $name = Arr::get($field, 'name');
            $type = Arr::get($field, 'type');
            $disk = Arr::get($field, 'disk', 'public');

            if (! $name || ! array_key_exists($name, $data)) {
                continue;
            }

            if (! in_array($type, ['file', 'image'], true) || $disk !== 'public') {
                continue;
            }

            $data[$name] = self::stripPublicPrefix($data[$name]);
        }

        return $data;
    }

    protected static function prefixPublicPath(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => self::prefixPublicPath($item), $value);
        }

        if (! is_string($value) || $value === '' || str_starts_with($value, 'storage/')) {
            return $value;
        }

        return 'storage/' . ltrim($value, '/');
    }

    protected static function stripPublicPrefix(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => self::stripPublicPrefix($item), $value);
        }

        if (! is_string($value)) {
            return $value;
        }

        return str_starts_with($value, 'storage/')
            ? substr($value, 8)
            : $value;
    }
}
