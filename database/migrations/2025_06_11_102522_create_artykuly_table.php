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
        Schema::create('artykuly', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa', 255);
            $table->string('kod_artykulu', 50)->nullable();
            $table->string('jednostka', 20)->default('szt.');
            $table->decimal('stawka_vat', 5, 2);
            $table->decimal('cena_netto', 10, 2);
            $table->decimal('cena_brutto', 10, 2);
            $table->text('opis')->nullable();
            $table->boolean('aktywny')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artykuly');
    }
};
