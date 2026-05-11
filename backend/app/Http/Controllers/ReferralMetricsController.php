<?php

namespace App\Http\Controllers;

use App\Database;

class ReferralMetricsController extends BaseController {

    public function getLinks() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Count unique referral links created
        $query = "SELECT COUNT(DISTINCT customer_id) as value FROM loyalty_customers 
                  WHERE store_id = $store_id 
                  AND ref_link_name IS NOT NULL
                  AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $this->response(['value' => $data['value'] ?? 0]);
    }

    public function getConversions() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Count referral conversions (orders with referred_by field)
        $query = "SELECT COUNT(*) as value FROM orders 
                  WHERE store_id = $store_id 
                  AND referred_by IS NOT NULL 
                  AND referred_by != ''
                  AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $this->response($data);
    }

    public function getRevenue() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Get daily referral revenue
        $query = "SELECT 
                    DATE(order_time) as date,
                    ROUND(SUM(order_total), 2) as revenue
                  FROM orders
                  WHERE store_id = $store_id
                  AND referred_by IS NOT NULL 
                  AND referred_by != ''
                  AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'
                  GROUP BY DATE(order_time)
                  ORDER BY date ASC";
        $result = Database::query($query);
        $data = [];

        while ($row = Database::fetchAssoc($result)) {
            $data[] = $row;
        }

        $this->response(['data' => $data]);
    }

    public function getTopReferrers() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Get top referrers by revenue
        $query = "SELECT 
                    lc.customer_name as name,
                    lc.customer_email as email,
                    COUNT(DISTINCT o.order_id) as conversions,
                    ROUND(SUM(o.order_total), 2) as revenue
                  FROM loyalty_customers lc
                  LEFT JOIN orders o ON o.referred_by = lc.ref_link_name 
                    AND o.store_id = lc.store_id
                    AND DATE(o.order_time) BETWEEN '$start_date' AND '$end_date'
                  WHERE lc.store_id = $store_id
                  AND lc.ref_link_name IS NOT NULL
                  GROUP BY lc.customer_id
                  ORDER BY revenue DESC
                  LIMIT 10";
        $result = Database::query($query);
        $data = [];

        while ($row = Database::fetchAssoc($result)) {
            $data[] = $row;
        }

        $this->response(['data' => $data]);
    }
}
