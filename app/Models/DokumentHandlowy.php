<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DokumentHandlowy extends Model
{
    use HasFactory;

    protected $table = 'dokumenty_handlowe';

    protected $fillable = [
        'numer',
        'typ_dokumentu',
        'data_sprzedazy',
        'data_wystawienia',
        'termin_platnosci',
        'miejsce_wystawienia',
        'id_firma',
        'id_odbiorca',
        'waluta',
        'kurs_waluty',
        'uwagi',
        'wartosc_netto',
        'wartosc_vat',
        'wartosc_brutto',
        'status'
    ];

    protected $casts = [
        'data_sprzedazy' => 'date',
        'data_wystawienia' => 'date',
        'termin_platnosci' => 'date',
        'kurs_waluty' => 'decimal:4',
        'wartosc_netto' => 'decimal:2',
        'wartosc_vat' => 'decimal:2',
        'wartosc_brutto' => 'decimal:2'
    ];

    /**
     * Pobierz firmę powiązaną z dokumentem
     */
    public function firma(): BelongsTo
    {
        return $this->belongsTo(Firma::class, 'id_firma');
    }

    /**
     * Pobierz odbiorcę powiązanego z dokumentem
     */
    public function odbiorca(): BelongsTo
    {
        return $this->belongsTo(Odbiorca::class, 'id_odbiorca');
    }

    /**
     * Pobierz pozycje dokumentu
     */
    public function pozycje(): HasMany
    {
        return $this->hasMany(PozycjaDokumentu::class, 'id_dokumentu');
    }
}