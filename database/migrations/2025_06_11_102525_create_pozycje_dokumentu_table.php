<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pozycje_dokumentu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_dokumentu')->constrained('dokumenty_handlowe');
            $table->foreignId('id_artykulu')->constrained('artykuly');
            $table->string('nazwa_pozycji', 255)->comment('kopia z artykułu');
            $table->decimal('ilosc', 10, 3);
            $table->string('jednostka', 20)->default('szt.');
            $table->decimal('cena_netto', 10, 2);
            $table->decimal('stawka_vat', 5, 2);
            $table->decimal('wartosc_vat', 10, 2);
            $table->decimal('cena_brutto', 10, 2);
            $table->decimal('wartosc_netto_pozycji', 12, 2);
            $table->decimal('wartosc_brutto_pozycji', 12, 2);
            $table->text('uwagi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pozycje_dokumentu');
    }
};
