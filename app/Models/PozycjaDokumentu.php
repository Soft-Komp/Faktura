<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PozycjaDokumentu extends Model
{
    use HasFactory;

    protected $table = 'pozycje_dokumentu';

    protected $fillable = [
        'id_dokumentu',
        'id_artykulu',
        'nazwa_pozycji',
        'ilosc',
        'jednostka',
        'cena_netto',
        'stawka_vat',
        'wartosc_vat',
        'cena_brutto',
        'wartosc_netto_pozycji',
        'wartosc_brutto_pozycji',
        'uwagi'
    ];

    protected $casts = [
        'ilosc' => 'decimal:3',
        'cena_netto' => 'decimal:2',
        'stawka_vat' => 'decimal:2',
        'wartosc_vat' => 'decimal:2',
        'cena_brutto' => 'decimal:2',
        'wartosc_netto_pozycji' => 'decimal:2',
        'wartosc_brutto_pozycji' => 'decimal:2'
    ];

    /**
     * Pobierz dokument powiązany z pozycją
     */
    public function dokument(): BelongsTo
    {
        return $this->belongsTo(DokumentHandlowy::class, 'id_dokumentu');
    }

    /**
     * Pobierz artykuł powiązany z pozycją
     */
    public function artykul(): BelongsTo
    {
        return $this->belongsTo(Artykul::class, 'id_artykulu');
    }
}