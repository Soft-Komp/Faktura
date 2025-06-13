<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Firma extends Model {

    use HasFactory;

    protected $table = 'firmy';
    protected $fillable = [
        'nazwa',
        'nazwa_pelna',
        'kod_pocztowy',
        'miejscowosc',
        'adres',
        'nip',
        'miejsce_wystawienia',
        'telefon',
        'email',
        'waluta_domyslna',
        'prefix_faktury',
        'typ', // <- Dodaj to jeśli nie ma
        'firma_ksiegowa_id'
    ];

    /**
     * Pobierz dokumenty handlowe powiązane z firmą
     */
    public function dokumentyHandlowe(): HasMany {
        return $this->hasMany(DokumentHandlowy::class, 'id_firma');
    }

    /**
     * Pobierz klientów firmy księgowej
     */
    public function klienci(): HasMany {
        return $this->hasMany(Firma::class, 'firma_ksiegowa_id');
    }

    /**
     * Pobierz firmę księgową klienta
     */
    public function firmaKsiegowa(): BelongsTo {
        return $this->belongsTo(Firma::class, 'firma_ksiegowa_id');
    }

    /**
     * Sprawdź czy firma jest firmą księgową
     */
    public function jestFirmaKsiegowa(): bool {
        return $this->typ === 'ksiegowa';
    }

    /**
     * Sprawdź czy firma jest klientem
     */
    public function jestKlient(): bool {
        return $this->typ === 'klient';
    }

}
