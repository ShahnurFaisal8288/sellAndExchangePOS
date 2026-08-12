<?php
// app/Helpers/NumberToWords.php
namespace App\Helpers;

class NumberToWords
{
    protected static array $ones = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen',
    ];

    protected static array $tens = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety',
    ];

    // Bangladeshi numbering: Lakh, Crore
    public static function convert(float $amount): string
    {
        $amount = (int) round($amount);

        if ($amount === 0) {
            return 'ZERO TAKA ONLY';
        }

        $crore = intdiv($amount, 10000000);
        $amount %= 10000000;
        $lakh = intdiv($amount, 100000);
        $amount %= 100000;
        $thousand = intdiv($amount, 1000);
        $amount %= 1000;
        $hundred = intdiv($amount, 100);
        $rest = $amount % 100;

        $parts = [];

        if ($crore) $parts[] = self::twoDigit($crore) . ' Crore';
        if ($lakh) $parts[] = self::twoDigit($lakh) . ' Lakh';
        if ($thousand) $parts[] = self::twoDigit($thousand) . ' Thousand';
        if ($hundred) $parts[] = self::ones()[$hundred] . ' Hundred';
        if ($rest) $parts[] = self::twoDigit($rest);

        return strtoupper(implode(' ', $parts) . ' Taka Only');
    }

    protected static function twoDigit(int $n): string
    {
        if ($n < 20) {
            return self::ones()[$n];
        }
        $t = intdiv($n, 10);
        $o = $n % 10;
        return trim(self::tens()[$t] . ' ' . self::ones()[$o]);
    }

    protected static function ones(): array { return self::$ones; }
    protected static function tens(): array { return self::$tens; }
}
