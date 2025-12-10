<?php

namespace App\Helpers;

use App\Models\Order;
use App\Models\PurchaseOrder;

class OrderHelper
{
    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $year = date('Y');
        $lastOrder = Order::where('order_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%06d', $prefix, $year, $newNumber);
    }

    public static function generatePONumber()
    {
        $prefix = 'PO';
        $year = date('Y');
        $lastPO = PurchaseOrder::where('po_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPO) {
            $lastNumber = (int) substr($lastPO->po_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%06d', $prefix, $year, $newNumber);
    }

    public static function generatePrescriptionNumber()
    {
        $prefix = 'RX';
        $year = date('Y');
        $lastPrescription = \App\Models\Prescription::where('prescription_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPrescription) {
            $lastNumber = (int) substr($lastPrescription->prescription_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%06d', $prefix, $year, $newNumber);
    }
}

