<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Zmień enum na varchar tymczasowo
        DB::statement("ALTER TABLE dokumenty_handlowe MODIFY COLUMN typ_dokumentu VARCHAR(10)");
        
        // Teraz zmień z powrotem na enum z nowymi wartościami
        DB::statement("ALTER TABLE dokumenty_handlowe MODIFY COLUMN typ_dokumentu ENUM('FV', 'FK', 'FMP', 'FMK', 'PA', 'WZ', 'PZ')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE dokumenty_handlowe MODIFY COLUMN typ_dokumentu ENUM('faktura_vat', 'faktura_eu', 'rachunek')");
    }
};