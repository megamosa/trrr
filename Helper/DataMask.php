<?php
namespace MagoArab\OrderTracking\Helper;

class DataMask
{
    public function maskEmail($email)
    {
        if (strpos($email, '@') === false) {
            return $email;
        }
        
        list($name, $domain) = explode('@', $email);
        $nameLength = strlen($name);
        
        if ($nameLength <= 2) {
            $maskedName = str_repeat('*', $nameLength);
        } else {
            $maskedName = substr($name, 0, 1) . str_repeat('*', $nameLength - 2) . substr($name, -1);
        }
        
        return $maskedName . '@' . $domain;
    }

    public function maskPhone($phone)
    {
        $phoneLength = strlen($phone);
        if ($phoneLength <= 4) {
            return str_repeat('*', $phoneLength);
        }
        
        return substr($phone, 0, 2) . str_repeat('*', $phoneLength - 4) . substr($phone, -2);
    }

    public function maskAddress($address)
    {
        $words = explode(' ', $address);
        $wordCount = count($words);
        
        if ($wordCount <= 2) {
            return $address;
        }
        
        $masked = array_slice($words, 0, 1);
        for ($i = 1; $i < $wordCount - 1; $i++) {
            $masked[] = str_repeat('*', strlen($words[$i]));
        }
        $masked[] = end($words);
        
        return implode(' ', $masked);
    }

    public function maskCreditCard($cardNumber)
    {
        if (strlen($cardNumber) <= 4) {
            return $cardNumber;
        }
        
        return str_repeat('*', strlen($cardNumber) - 4) . substr($cardNumber, -4);
    }
}