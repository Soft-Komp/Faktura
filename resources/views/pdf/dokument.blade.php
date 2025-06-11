<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $dokument->numer }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { margin-bottom: 30px; }
        .document-title { font-size: 20px; font-weight: bold; text-align: center; margin: 20px 0; }
        .company-details, .recipient-details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #f2f2f2; }
        .summary { margin-top: 30px; text-align: right; }
        .footer { margin-top: 50px; font-size: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="document-title">{{ $dokument->typ_dokumentu }} {{ $dokument->numer }}</div>
        <div>Data wystawienia: {{ $dokument->data_wystawienia }}</div>
        <div>Miejsce wystawienia: {{ $dokument->miejsce_wystawienia }}</div>
        <div>Data sprzedaży: {{ $dokument->data_sprzedazy }}</div>
    </div>
    
    <div class="company-details">
        <h3>Sprzedawca:</h3>
        <div>{{ $dokument->firma->nazwa_pelna }}</div>
        <div>{{ $dokument->firma->adres }}</div>
        <div>{{ $dokument->firma->kod_pocztowy }} {{ $dokument->firma->miejscowosc }}</div>
        <div>NIP: {{ $dokument->firma->nip }}</div>
    </div>
    
    <div class="recipient-details">
        <h3>Nabywca:</h3>
        <div>{{ $dokument->odbiorca->nazwa_pelna }}</div>
        <div>{{ $dokument->odbiorca->adres }}</div>
        <div>{{ $dokument->odbiorca->kod_pocztowy }} {{ $dokument->odbiorca->miejscowosc }}</div>
        <div>NIP: {{ $dokument->odbiorca->nip }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Lp.</th>
                <th>Nazwa</th>
                <th>Ilość</th>
                <th>Jednostka</th>
                <th>Cena netto</th>
                <th>Wartość netto</th>
                <th>Stawka VAT</th>
                <th>Kwota VAT</th>
                <th>Wartość brutto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dokument->pozycje as $index => $pozycja)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $pozycja->nazwa_pozycji }}</td>
                <td>{{ $pozycja->ilosc }}</td>
                <td>{{ $pozycja->jednostka }}</td>
                <td>{{ number_format($pozycja->cena_netto, 2) }} {{ $dokument->waluta }}</td>
                <td>{{ number_format($pozycja->wartosc_netto, 2) }} {{ $dokument->waluta }}</td>
                <td>{{ $pozycja->stawka_vat }}%</td>
                <td>{{ number_format($pozycja->kwota_vat, 2) }} {{ $dokument->waluta }}</td>
                <td>{{ number_format($pozycja->wartosc_brutto, 2) }} {{ $dokument->waluta }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="summary">
        <div>Razem netto: {{ number_format($dokument->wartosc_netto, 2) }} {{ $dokument->waluta }}</div>
        <div>Razem VAT: {{ number_format($dokument->kwota_vat, 2) }} {{ $dokument->waluta }}</div>
        <div>Razem brutto: {{ number_format($dokument->wartosc_brutto, 2) }} {{ $dokument->waluta }}</div>
    </div>
    
    <div class="payment">
        <h3>Płatność:</h3>
        <div>Termin płatności: {{ $dokument->termin_platnosci }}</div>
        <div>Forma płatności: {{ $dokument->forma_platnosci ?? 'Przelew' }}</div>
        @if($dokument->numer_konta)
        <div>Numer konta: {{ $dokument->numer_konta }}</div>
        @endif
    </div>
    
    <div class="footer">
        <p>{{ $dokument->uwagi }}</p>
        <p>Dokument wygenerowany elektronicznie.</p>
    </div>
</body>
</html>