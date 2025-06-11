<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'firma_id',
        'rola'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Pobierz firmę użytkownika
     */
    public function firma(): BelongsTo
    {
        return $this->belongsTo(Firma::class, 'firma_id');
    }
    
    /**
     * Sprawdź czy użytkownik jest administratorem
     */
    public function jestAdmin(): bool
    {
        return $this->rola === 'admin';
    }
    
    /**
     * Sprawdź czy użytkownik jest księgowym
     */
    public function jestKsiegowy(): bool
    {
        return $this->rola === 'ksiegowy';
    }
    
    /**
     * Sprawdź czy użytkownik jest klientem
     */
    public function jestKlient(): bool
    {
        return $this->rola === 'klient';
    }
    
    /**
     * Sprawdź czy użytkownik może wystawiać faktury w imieniu danej firmy
     */
    public function mozeTworzyFakturyDlaFirmy($firmaId): bool
    {
        // Użytkownik może zawsze tworzyć faktury dla swojej firmy
        if ($this->firma_id == $firmaId) {
            return true;
        }
        
        // Jeśli użytkownik jest z firmy księgowej, sprawdź czy obsługuje daną firmę
        if ($this->firma && $this->firma->jestFirmaKsiegowa()) {
            $firma = Firma::find($firmaId);
            return $firma && $firma->firma_ksiegowa_id == $this->firma_id;
        }
        
        return false;
    }
}