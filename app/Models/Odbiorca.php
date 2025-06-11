<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Odbiorca extends Model
{
    use HasFactory;

    protected $table = 'odbiorcy';

    protected $fillable = [
        'nazwa',
        'nazwa_pelna',
        'kod_pocztowy',
        'miejscowosc',
        'adres',
        'nip',
        'telefon',
        'email',
        'kraj',
        'typ_odbiorcy'
    ];

    /**
     * Pobierz dokumenty handlowe powiązane z odbiorcą
     */
    public function dokumentyHandlowe(): HasMany
    {
        return $this->hasMany(DokumentHandlowy::class, 'id_odbiorca');
    }
}