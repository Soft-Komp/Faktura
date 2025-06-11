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
        Schema::create('odbiorcy', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa', 100);
            $table->string('nazwa_pelna', 255);
            $table->string('kod_pocztowy', 10);
            $table->string('miejscowosc', 100);
            $table->string('adres', 255);
            $table->string('nip', 15)->nullable();
            $table->string('telefon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('kraj', 100)->default('Polska');
            $table->enum('typ_odbiorcy', ['krajowy', 'ue', 'pozaue']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odbiorcy');
    }
};
