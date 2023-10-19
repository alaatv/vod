<?php


namespace App\Repositories;


use Exception;

class InvoiceRepository
{

    public static function hasProducts(array $products, $invoice): bool
    {
        try {
            $invoiceProducts = $invoice['items'][0]['orderproducts']->pluck('id')->toArray();
            return array_intersect($invoiceProducts, $products) ? true : false;
        } catch (Exception $e) {
            return false;
        }

    }
}
