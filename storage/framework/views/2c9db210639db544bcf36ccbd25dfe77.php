<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($dokument->numer); ?></title>
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
        <div class="document-title"><?php echo e($dokument->typ_dokumentu); ?> <?php echo e($dokument->numer); ?></div>
        <div>Data wystawienia: <?php echo e($dokument->data_wystawienia); ?></div>
        <div>Miejsce wystawienia: <?php echo e($dokument->miejsce_wystawienia); ?></div>
        <div>Data sprzedaży: <?php echo e($dokument->data_sprzedazy); ?></div>
    </div>
    
    <div class="company-details">
        <h3>Sprzedawca:</h3>
        <div><?php echo e($dokument->firma->nazwa_pelna); ?></div>
        <div><?php echo e($dokument->firma->adres); ?></div>
        <div><?php echo e($dokument->firma->kod_pocztowy); ?> <?php echo e($dokument->firma->miejscowosc); ?></div>
        <div>NIP: <?php echo e($dokument->firma->nip); ?></div>
    </div>
    
    <div class="recipient-details">
        <h3>Nabywca:</h3>
        <div><?php echo e($dokument->odbiorca->nazwa_pelna); ?></div>
        <div><?php echo e($dokument->odbiorca->adres); ?></div>
        <div><?php echo e($dokument->odbiorca->kod_pocztowy); ?> <?php echo e($dokument->odbiorca->miejscowosc); ?></div>
        <div>NIP: <?php echo e($dokument->odbiorca->nip); ?></div>
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
            <?php $__currentLoopData = $dokument->pozycje; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pozycja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($pozycja->nazwa_pozycji); ?></td>
                <td><?php echo e($pozycja->ilosc); ?></td>
                <td><?php echo e($pozycja->jednostka); ?></td>
                <td><?php echo e(number_format($pozycja->cena_netto, 2)); ?> <?php echo e($dokument->waluta); ?></td>
                <td><?php echo e(number_format($pozycja->wartosc_netto, 2)); ?> <?php echo e($dokument->waluta); ?></td>
                <td><?php echo e($pozycja->stawka_vat); ?>%</td>
                <td><?php echo e(number_format($pozycja->kwota_vat, 2)); ?> <?php echo e($dokument->waluta); ?></td>
                <td><?php echo e(number_format($pozycja->wartosc_brutto, 2)); ?> <?php echo e($dokument->waluta); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    
    <div class="summary">
        <div>Razem netto: <?php echo e(number_format($dokument->wartosc_netto, 2)); ?> <?php echo e($dokument->waluta); ?></div>
        <div>Razem VAT: <?php echo e(number_format($dokument->kwota_vat, 2)); ?> <?php echo e($dokument->waluta); ?></div>
        <div>Razem brutto: <?php echo e(number_format($dokument->wartosc_brutto, 2)); ?> <?php echo e($dokument->waluta); ?></div>
    </div>
    
    <div class="payment">
        <h3>Płatność:</h3>
        <div>Termin płatności: <?php echo e($dokument->termin_platnosci); ?></div>
        <div>Forma płatności: <?php echo e($dokument->forma_platnosci ?? 'Przelew'); ?></div>
        <?php if($dokument->numer_konta): ?>
        <div>Numer konta: <?php echo e($dokument->numer_konta); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="footer">
        <p><?php echo e($dokument->uwagi); ?></p>
        <p>Dokument wygenerowany elektronicznie.</p>
    </div>
</body>
</html><?php /**PATH C:\projects\Faktura2\resources\views\pdf\dokument.blade.php ENDPATH**/ ?>