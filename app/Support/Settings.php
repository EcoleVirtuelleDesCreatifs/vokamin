<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class Settings
{
    protected ?array $values = null;

    public function all(): array
    {
        if ($this->values === null) {
            $stored = [];
            try {
                $stored = Cache::rememberForever('vokamin.settings', fn () => Schema::hasTable('settings')
                    ? Setting::query()->pluck('value', 'key')->all() : []);
            } catch (Throwable) {
                // Base indisponible (installation en cours) : valeurs par défaut.
            }
            $this->values = array_merge(config('vokamin.defaults', []), array_filter($stored, fn ($v) => $v !== null));
        }

        return $this->values;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->all()[$key] ?? $default;
    }

    public function set(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget('vokamin.settings');
        $this->values = null;
    }
}
