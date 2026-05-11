<?php

namespace App\Http\Controllers;

use App\Database;

class ExportController extends BaseController {

    public function export() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $type = $_GET['type'] ?? 'csv';
        $metrics = explode(',', $_GET['metrics'] ?? 'promos,loyalty,referrals');
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        $filename = 'flowanalytics_' . date('Y-m-d_His') . '.' . $type;
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        // Header row
        $headers = ['Date', 'Metric', 'Value'];
        fputcsv($output, $headers);

        // Add data based on selected metrics
        if (in_array('promos', $metrics)) {
            $this->exportPromoData($output, $store_id, $start_date, $end_date);
        }
        if (in_array('loyalty', $metrics)) {
            $this->exportLoyaltyData($output, $store_id, $start_date, $end_date);
        }
        if (in_array('referrals', $metrics)) {
            $this->exportReferralData($output, $store_id, $start_date, $end_date);
        }

        fclose($output);
        exit;
    }

    private function exportPromoData($output, $store_id, $start_date, $end_date) {
        $query = "SELECT 
                    DATE(order_time) as date,
                    'FlowPromo - Claimed' as metric,
                    COUNT(*) as value
                  FROM orders
                  WHERE store_id = $store_id AND FlowPromo = 1
                  AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'
                  GROUP BY DATE(order_time)";
        $result = Database::query($query);
        while ($row = Database::fetchAssoc($result)) {
            fputcsv($output, [$row['date'], $row['metric'], $row['value']]);
        }
    }

    private function exportLoyaltyData($output, $store_id, $start_date, $end_date) {
        $query = "SELECT 
                    DATE(created_at) as date,
                    'FlowLoyalty - Stamps Awarded' as metric,
                    SUM(stamps_awarded) as value
                  FROM loyalty_trust_log
                  WHERE store_id = $store_id AND stamp_type = 'purchased'
                  AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'
                  GROUP BY DATE(created_at)";
        $result = Database::query($query);
        while ($row = Database::fetchAssoc($result)) {
            fputcsv($output, [$row['date'], $row['metric'], $row['value']]);
        }
    }

    private function exportReferralData($output, $store_id, $start_date, $end_date) {
        $query = "SELECT 
                    DATE(order_time) as date,
                    'FlowLink - Referral Revenue' as metric,
                    ROUND(SUM(order_total), 2) as value
                  FROM orders
                  WHERE store_id = $store_id
                  AND referred_by IS NOT NULL AND referred_by != ''
                  AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'
                  GROUP BY DATE(order_time)";
        $result = Database::query($query);
        while ($row = Database::fetchAssoc($result)) {
            fputcsv($output, [$row['date'], $row['metric'], $row['value']]);
        }
    }
}
