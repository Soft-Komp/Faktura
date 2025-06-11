<?php

namespace App\Http\Controllers\API;

use App\Models\DokumentHandlowy;
use App\Models\Firma;
use App\Models\Odbiorca;
use App\Models\Artykul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends BaseController
{
    /**
     * Pobierz statystyki dla dashboardu
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Statystyki dokumentów
        $dokumentyStats = [
            'total' => DokumentHandlowy::count(),
            'wystawione' => DokumentHandlowy::where('status', 'wystawiony')->count(),
            'zatwierdzone' => DokumentHandlowy::where('status', 'zatwierdzony')->count(),
            'anulowane' => DokumentHandlowy::where('status', 'anulowany')->count(),
        ];
        
        // Statystyki wartości dokumentów w bieżącym miesiącu
        $biezacyMiesiac = now()->startOfMonth();
        $koniecMiesiaca = now()->endOfMonth();
        
        $wartosciMiesiaca = DokumentHandlowy::whereBetween('data_wystawienia', [$biezacyMiesiac, $koniecMiesiaca])
            ->select(DB::raw('SUM(wartosc_netto) as suma_netto, SUM(wartosc_brutto) as suma_brutto'))
            ->first();
            
        // Statystyki wartości dokumentów w poprzednim miesiącu
        $poprzedniMiesiac = now()->subMonth()->startOfMonth();
        $koniecPoprzedniego = now()->subMonth()->endOfMonth();
        
        $wartosciPoprzedniego = DokumentHandlowy::whereBetween('data_wystawienia', [$poprzedniMiesiac, $koniecPoprzedniego])
            ->select(DB::raw('SUM(wartosc_netto) as suma_netto, SUM(wartosc_brutto) as suma_brutto'))
            ->first();
        
        // Statystyki według typów dokumentów
        $wedlugTypu = DokumentHandlowy::select('typ_dokumentu', DB::raw('COUNT(*) as ilosc'))
            ->groupBy('typ_dokumentu')
            ->get()
            ->pluck('ilosc', 'typ_dokumentu')
            ->toArray();
        
        // Najnowsze dokumenty
        $najnowszeDokumenty = DokumentHandlowy::with(['firma', 'odbiorca'])
            ->orderBy('data_wystawienia', 'desc')
            ->limit(5)
            ->get();
        
        // Liczba firm, odbiorców i artykułów
        $liczbaFirm = Firma::count();
        $liczbaOdbiorcow = Odbiorca::count();
        $liczbaArtykulow = Artykul::count();
        
        // Najczęściej używane artykuły
        $popularneArtykuly = DB::table('pozycje_dokumentu')
            ->select('artykuly.nazwa', 'artykuly.kod_artykulu', DB::raw('COUNT(*) as ilosc_uzyc'))
            ->join('artykuly', 'pozycje_dokumentu.id_artykulu', '=', 'artykuly.id')
            ->groupBy('artykuly.id', 'artykuly.nazwa', 'artykuly.kod_artykulu')
            ->orderBy('ilosc_uzyc', 'desc')
            ->limit(5)
            ->get();
        
        // Najczęściej wybierani odbiorcy
        $popularniOdbiorcy = DB::table('dokumenty_handlowe')
            ->select('odbiorcy.nazwa', DB::raw('COUNT(*) as ilosc_dokumentow'))
            ->join('odbiorcy', 'dokumenty_handlowe.id_odbiorca', '=', 'odbiorcy.id')
            ->groupBy('odbiorcy.id', 'odbiorcy.nazwa')
            ->orderBy('ilosc_dokumentow', 'desc')
            ->limit(5)
            ->get();
        
        $stats = [
            'dokumenty' => $dokumentyStats,
            'wartosc_biezacy_miesiac' => [
                'netto' => $wartosciMiesiaca->suma_netto ?? 0,
                'brutto' => $wartosciMiesiaca->suma_brutto ?? 0,
            ],
            'wartosc_poprzedni_miesiac' => [
                'netto' => $wartosciPoprzedniego->suma_netto ?? 0,
                'brutto' => $wartosciPoprzedniego->suma_brutto ?? 0,
            ],
            'wedlug_typu' => $wedlugTypu,
            'najnowsze_dokumenty' => $najnowszeDokumenty,
            'liczba_firm' => $liczbaFirm,
            'liczba_odbiorcow' => $liczbaOdbiorcow,
            'liczba_artykulow' => $liczbaArtykulow,
            'popularne_artykuly' => $popularneArtykuly,
            'popularni_odbiorcy' => $popularniOdbiorcy,
        ];
        
        return $this->sendResponse($stats, 'Statystyki pobrane pomyślnie');
    }
}