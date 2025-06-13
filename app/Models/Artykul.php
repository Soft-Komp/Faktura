<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'aktywny',
        'firma_id',
        'publiczny'
    ];

    protected $casts = [
        'aktywny' => 'boolean',
        'publiczny' => 'boolean',
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

    /**
     * Pobierz firmę właściciela artykułu
     */
    public function firma(): BelongsTo
    {
        return $this->belongsTo(Firma::class, 'firma_id');
    }

    /**
     * Scope - tylko aktywne artykuły
     */
    public function scopeAktywne($query)
    {
        return $query->where('aktywny', true);
    }

    /**
     * Scope - artykuły publiczne
     */
    public function scopePubliczne($query)
    {
        return $query->where('publiczny', true);
    }

    /**
     * Scope - artykuły dla konkretnej firmy
     */
    public function scopeDlaFirmy($query, $firmaId)
    {
        return $query->where(function($q) use ($firmaId) {
            $q->where('firma_id', $firmaId)
              ->orWhere('publiczny', true)
              ->orWhereNull('firma_id'); // Artykuły bez przypisania do firmy (legacy)
        });
    }

    /**
     * Sprawdź czy artykuł należy do firmy lub jest publiczny
     */
    public function dostepnyDlaFirmy($firmaId): bool
    {
        return $this->firma_id == $firmaId || 
               $this->publiczny || 
               is_null($this->firma_id);
    }

    /**
     * Sprawdź czy artykuł może być edytowany przez użytkownika
     */
    public function mozeBycEdytowanyPrzez($user): bool
    {
        // Admin może edytować wszystko
        if ($user->jestAdmin()) {
            return true;
        }

        // Właściciel może edytować swoje artykuły
        if ($this->firma_id == $user->firma_id) {
            return true;
        }

        // Księgowy może edytować artykuły swoich klientów
        if ($user->jestKsiegowy() && $user->firma->jestFirmaKsiegowa()) {
            $firma = $this->firma;
            if ($firma && $firma->firma_ksiegowa_id == $user->firma_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Boot method - ustawienia domyślne
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artykul) {
            // Jeśli nie ustawiono firma_id, przypisz do firmy użytkownika
            if (is_null($artykul->firma_id) && auth()->check()) {
                $artykul->firma_id = auth()->user()->firma_id;
            }

            // Domyślnie artykuły nie są publiczne
            if (is_null($artykul->publiczny)) {
                $artykul->publiczny = false;
            }
        });
    }
}