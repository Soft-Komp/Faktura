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
        Schema::table('artykuly', function (Blueprint $table) {
            $table->foreignId('firma_id')->nullable()->after('id')->comment('ID firmy właściciela artykułu');
            $table->boolean('publiczny')->default(false)->after('aktywny')->comment('Czy artykuł jest dostępny dla wszystkich firm');
            
            $table->foreign('firma_id')->references('id')->on('firmy')->onDelete('cascade');
            $table->index(['firma_id', 'aktywny']);
            $table->index('publiczny');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artykuly', function (Blueprint $table) {
            $table->dropForeign(['firma_id']);
            $table->dropIndex(['firma_id', 'aktywny']);
            $table->dropIndex(['publiczny']);
            $table->dropColumn(['firma_id', 'publiczny']);
        });
    }
};
