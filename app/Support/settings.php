<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Obtiene un setting por key.
     * - Si el valor se guardó como {"v": algo}, devuelve "algo"
     * - Si el valor es un array/obj (json), lo devuelve completo
     */
    function setting(string $key, $default = null)
    {
        $row = Setting::query()->where('key', $key)->first();
        if (! $row) {
            return $default;
        }

        $val = $row->value;

        // Caso: guardado como {"v": ...}
        if (is_array($val) && array_key_exists('v', $val)) {
            return $val['v'];
        }

        return $val ?? $default;
    }
}

if (! function_exists('setting_set')) {
    /**
     * Guarda/actualiza un setting.
     * - Si mandas un valor simple, lo guarda como {"v": valor}
     * - Si mandas array, lo guarda tal cual (json)
     */
    function setting_set(string $key, $value): void
    {
        $payload = is_array($value) ? $value : ['v' => $value];

        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $payload]
        );
    }
}

if (! function_exists('setting_forget')) {
    /**
     * Elimina un setting (útil para reset o limpieza).
     */
    function setting_forget(string $key): void
    {
        Setting::query()->where('key', $key)->delete();
    }
}
