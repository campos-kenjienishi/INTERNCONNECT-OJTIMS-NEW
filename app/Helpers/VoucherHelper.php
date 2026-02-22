<?php
if (!function_exists('generateVoucherCode')) {
    function generateVoucherCode($length = 8) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $voucherCode = '';

        for ($i = 0; $i < $length; $i++) {
            $voucherCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $voucherCode;
    }
}