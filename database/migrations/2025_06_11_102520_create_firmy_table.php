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
        Schema::create('firmy', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa', 100)->comment('nazwa skrócona');
            $table->string('nazwa_pelna', 255);
            $table->string('kod_pocztowy', 10);
            $table->string('miejscowosc', 100);
            $table->string('adres', 255);
            $table->string('nip', 15)->unique();
            $table->string('miejsce_wystawienia', 100);
            $table->string('telefon', 20);
            $table->string('email', 100);
            $table->string('waluta_domyslna', 3)->default('PLN');
            $table->string('prefix_faktury', 10)->comment('prefix dla numeracji');
            $table->enum('typ', ['ksiegowa', 'klient'])->default('klient')->comment('typ firmy');
            $table->foreignId('firma_ksiegowa_id')->nullable()->comment('ID firmy księgowej, jeśli to jest klient');
            $table->timestamps();
            
            // Relacja do samej siebie - firma księgowa może mieć wielu klientów
            $table->foreign('firma_ksiegowa_id')->references('id')->on('firmy')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firmy');
    }
};
