<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Corrections relevées par l'audit du 23/11/2026 :
 * adresse uniformisée (cohérence NAP), ligne fax supprimée (même numéro que le
 * téléphone), faute « le mutuelle » dans le titre d'une actualité.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            $address = DB::table('settings')->where('key', 'address')->value('value');
            if ($address && stripos(str_replace('İ', 'I', $address), 'KAY Center') !== false) {
                DB::table('settings')->where('key', 'address')->update(['value' => 'Cocody Angré, 7ᵉ Tranche – Immeuble KAY Center']);
            }
            $digits = fn ($v) => preg_replace('/\D/', '', (string) $v);
            $fax = DB::table('settings')->where('key', 'fax')->value('value');
            $tels = DB::table('settings')->whereIn('key', ['tel1', 'tel2'])->pluck('value')->map($digits)->all();
            if ($fax !== null && ($fax === '' || in_array($digits($fax), array_merge($tels, [$digits(config('vokamin.defaults.tel1'))]), true))) {
                DB::table('settings')->updateOrInsert(['key' => 'fax'], ['value' => '']);
            }
            Cache::forget('vokamin.settings');
        }

        if (Schema::hasTable('posts')) {
            foreach (DB::table('posts')->where('title', 'like', '%le mutuelle%')->get(['id', 'title']) as $post) {
                DB::table('posts')->where('id', $post->id)->update(['title' => preg_replace('/\ble mutuelle\b/iu', 'la Mutuelle', $post->title)]);
            }
        }
    }

    public function down(): void
    {
        // Corrections de contenu : pas de retour arrière.
    }
};
