<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artykul extends Model
{
    use HasFactory;

    protected $table = 'artykuly';

    protected $fillable = [
        'nazwa',
        'kod_artykulu',
        'jednostka',
        'stawka_vat',
        'cena_netto',
        'cena_brutto',
        'opis',
        'aktywny'
    ];

    protected $casts = [
        'aktywny' => 'boolean',
        'stawka_vat' => 'decimal:2',
        'cena_netto' => 'decimal:2',
        'cena_brutto' => 'decimal:2'
    ];

    /**
     * Pobierz pozycje dokumentów powiązane z artykułem
     */
    public function pozycjeDokumentu(): HasMany
    {
        return $this->hasMany(PozycjaDokumentu::class, 'id_artykulu');
    }
}