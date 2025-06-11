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
        Schema::create('dokumenty_handlowe', function (Blueprint $table) {
            $table->id();
            $table->string('numer', 50)->unique();
            $table->enum('typ_dokumentu', ['faktura_vat', 'faktura_eu', 'rachunek']);
            $table->date('data_sprzedazy');
            $table->date('data_wystawienia');
            $table->date('termin_platnosci');
            $table->string('miejsce_wystawienia', 100);
            $table->foreignId('id_firma')->constrained('firmy');
            $table->foreignId('id_odbiorca')->constrained('odbiorcy');
            $table->string('waluta', 3)->default('PLN');
            $table->decimal('kurs_waluty', 10, 4)->default(1.0000);
            $table->text('uwagi')->nullable();
            $table->decimal('wartosc_netto', 12, 2);
            $table->decimal('wartosc_vat', 12, 2);
            $table->decimal('wartosc_brutto', 12, 2);
            $table->enum('status', ['robocza', 'wystawiona', 'wysłana', 'zapłacona']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumenty_handlowe');
    }
};
