# System Fakturowania dla Biur Rachunkowych

Profesjonalna aplikacja webowa do wystawiania faktur dla biur rachunkowych i ich klientów, zbudowana w Laravel + Vue.js 3.

## 🛠️ Stack Technologiczny

- **Backend:** Laravel (najnowsza stabilna wersja)
- **Frontend:** Vue.js 3 z Composition API
- **Baza danych:** MariaDB/MySQL
- **Architektura:** RESTful API + SPA
- **Stylowanie:** Tailwind CSS
- **State Management:** Pinia
- **Autentykacja:** Laravel Sanctum

## ⚡ Wymagania Systemowe

- PHP 8.1 lub nowszy
- Composer
- Node.js 16+ i npm
- MariaDB/MySQL 8.0+
- Git

## 🚀 Instalacja i Uruchomienie

### 1. Klonowanie Repozytorium

```bash
git clone [adres-repozytorium]
cd system-fakturowania
```

### 2. Instalacja Zależności Backend

```bash
# Instalacja pakietów PHP
composer install

# Kopiowanie pliku konfiguracyjnego
cp .env.example .env

# Generowanie klucza aplikacji
php artisan key:generate
```

### 3. Konfiguracja Bazy Danych

Edytuj plik `.env` i ustaw parametry bazy danych:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=system_fakturowania
DB_USERNAME=twoja_nazwa_uzytkownika
DB_PASSWORD=twoje_haslo
```

### 4. Migracje i Dane Testowe

```bash
# Utworzenie tabel w bazie danych
php artisan migrate

# Opcjonalnie - dane testowe
php artisan db:seed
```

### 5. Konfiguracja Sanctum

```bash
# Publikacja konfiguracji Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Dodanie middleware do kernel (jeśli nie ma)
# Sprawdź app/Http/Kernel.php czy zawiera:
# 'api' => [
#     \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
# ],
```

### 6. Instalacja Zależności Frontend

```bash
# Instalacja pakietów npm
npm install

# Kompilacja assetów (development)
npm run dev
```

### 7. Uruchomienie Aplikacji

**Terminal 1 - Backend:**
```bash
php artisan serve
```

**Terminal 2 - Frontend (w trybie development):**
```bash
npm run dev
```

**Aplikacja będzie dostępna pod adresem:** `http://localhost:8000`

## 👤 Pierwsze Uruchomienie

### Tworzenie Pierwszego Użytkownika

Po uruchomieniu aplikacji utwórz pierwszego użytkownika przez Tinker:

```bash
php artisan tinker
```

W konsoli Tinker wykonaj:

```php
use App\Models\User;
use App\Models\Firma;

// Utworzenie firmy księgowej
$firma = Firma::create([
    'nazwa' => 'Biuro Rachunkowe ABC',
    'nazwa_pelna' => 'Biuro Rachunkowe ABC Sp. z o.o.',
    'nip' => '1234567890',
    'adres' => 'ul. Główna 123',
    'kod_pocztowy' => '00-001',
    'miejscowosc' => 'Warszawa',
    'telefon' => '+48 123 456 789',
    'email' => 'biuro@abc.pl',
    'miejsce_wystawienia' => 'Warszawa',
    'prefix_faktury' => 'FV',
    'typ' => 'ksiegowa'
]);

// Utworzenie administratora
$admin = User::create([
    'name' => 'Administrator',
    'email' => 'admin@abc.pl',
    'password' => bcrypt('admin123'),
    'firma_id' => $firma->id,
    'rola' => 'admin'
]);

// Utworzenie księgowego
$ksiegowy = User::create([
    'name' => 'Jan Kowalski',
    'email' => 'ksiegowy@abc.pl',
    'password' => bcrypt('ksiegowy123'),
    'firma_id' => $firma->id,
    'rola' => 'ksiegowy'
]);

echo "Użytkownicy utworzeni pomyślnie!\n";
```

## 🔐 Dane Dostępowe

Po utworzeniu użytkowników testowych możesz zalogować się:

### Administrator
- **Email:** `admin@abc.pl`
- **Hasło:** `admin123`
- **Uprawnienia:** Pełne uprawnienia w systemie

### Księgowy
- **Email:** `ksiegowy@abc.pl`
- **Hasło:** `ksiegowy123`
- **Uprawnienia:** Zarządzanie firmami, klientami, dokumentami

### Klient (do utworzenia przez admin/księgowego)
- **Rola:** `klient`
- **Uprawnienia:** Wystawianie dokumentów tylko dla własnej firmy

## 📋 Dostępne Funkcjonalności

### Dla Administratorów
- Zarządzanie użytkownikami
- Zarządzanie firmami
- Zarządzanie wszystkimi dokumentami
- Dostęp do statystyk

### Dla Księgowych
- Zarządzanie klientami firmy księgowej
- Wystawianie dokumentów w imieniu klientów
- Zarządzanie odbiorcami i artykułami
- Generowanie raportów

### Dla Klientów
- Wystawianie dokumentów dla własnej firmy
- Zarządzanie odbiorcami i artykułami
- Przeglądanie własnych dokumentów

## 🗂️ Struktura Modułów

- **Dashboard** - Przegląd statystyk i szybkie akcje
- **Dokumenty** - Faktury VAT, EU, rachunki
- **Odbiorcy** - Baza klientów
- **Artykuły** - Katalog produktów/usług
- **Firmy** - Zarządzanie firmami (księgowe/klienci)
- **Użytkownicy** - Zarządzanie kontami (tylko admin)

## 🔧 Komendy Pomocnicze

### Development
```bash
# Kompilacja w trybie watch
npm run watch

# Czyszczenie cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Sprawdzanie logów
tail -f storage/logs/laravel.log
```

### Production
```bash
# Kompilacja dla produkcji
npm run build

# Optymalizacja Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migracje na produkcji
php artisan migrate --force
```

### Testy
```bash
# Uruchomienie testów
php artisan test

# Z pokryciem kodu
php artisan test --coverage
```

## 📁 Struktura Projektu

```
├── app/
│   ├── Http/Controllers/API/     # Kontrolery API
│   ├── Models/                   # Modele Eloquent
│   ├── Http/Middleware/          # Middleware
│   └── Http/Requests/            # Klasy walidacji
├── database/
│   ├── migrations/               # Migracje bazy danych
│   └── seeders/                  # Seedery
├── resources/
│   ├── js/
│   │   ├── components/           # Komponenty Vue
│   │   ├── views/               # Widoki/strony
│   │   ├── stores/              # Store Pinia
│   │   └── router/              # Routing Vue
│   └── css/                     # Stylowanie
└── routes/
    ├── api.php                  # Trasy API
    └── web.php                  # Trasy web
```

## 🌐 API Endpoints

### Autentykacja
- `POST /api/login` - Logowanie
- `POST /api/register` - Rejestracja
- `POST /api/logout` - Wylogowanie

### Dokumenty
- `GET /api/dokumenty` - Lista dokumentów
- `POST /api/dokumenty` - Nowy dokument
- `PUT /api/dokumenty/{id}` - Edycja dokumentu
- `DELETE /api/dokumenty/{id}` - Usunięcie dokumentu
- `POST /api/dokumenty/{id}/pdf` - Generowanie PDF

### Zarządzanie
- `GET /api/firmy` - Lista firm
- `GET /api/odbiorcy` - Lista odbiorców
- `GET /api/artykuly` - Lista artykułów
- `GET /api/stats` - Statystyki

## 🐛 Rozwiązywanie Problemów

### Błędy Bazy Danych
```bash
# Sprawdź połączenie
php artisan tinker
>>> DB::connection()->getPdo();
```

### Błędy Kompilacji Frontend
```bash
# Wyczyść cache npm
rm -rf node_modules package-lock.json
npm install
```

### Błędy Uprawnień
```bash
# Linux/Mac
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Błędy CORS
Sprawdź konfigurację w `config/cors.php` lub użyj tej samej domeny dla frontu i API.

## 📝 Licencja

Ten projekt jest objęty licencją MIT.

## 🤝 Wsparcie

W przypadku problemów:
1. Sprawdź logi w `storage/logs/laravel.log`
2. Sprawdź konsolę przeglądarki (F12)
3. Uruchom `php artisan queue:work` jeśli używasz kolejek

---

**Powodzenia z systemem fakturowania! 🚀**