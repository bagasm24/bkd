<?php
if (!function_exists('formatNumber')) {
    function formatNumber($number)
    {
        // Cek jika angka adalah bilangan bulat
        if (floor($number) == $number) {
            return (int)$number;
        }
        // Hapus angka nol di belakang desimal
        return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
    }
}
