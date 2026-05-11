<?php

namespace App\Http\Controllers;

use App\Database;

class LoyaltyMetricsController extends BaseController {

    public function getStamps() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Count total stamps issued in date range
        $query = "SELECT SUM(stamps_awarded) as value FROM loyalty_trust_log 
                  WHERE store_id = $store_id 
                  AND stamp_type = 'purchased'
                  AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $this->response(['value' => $data['value'] ?? 0]);
    }

    public function getRewards() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Count total rewards redeemed
        $query = "SELECT COUNT(*) as value FROM loyalty_trust_log 
                  WHERE store_id = $store_id 
                  AND stamp_type = 'reward_redeemed'
                  AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $this->response($data);
    }

    public function getRepeatRate() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Get unique customers and repeat customers
        $query = "SELECT 
                    COUNT(DISTINCT user_id) as total_customers,
                    COUNT(DISTINCT CASE WHEN order_count > 1 THEN user_id END) as repeat_customers
                  FROM (
                    SELECT user_id, COUNT(*) as order_count
                    FROM orders
                    WHERE store_id = $store_id
                    AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'
                    GROUP BY user_id
                  ) as customer_orders";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $rate = $data['total_customers'] > 0 ? 
            ($data['repeat_customers'] / $data['total_customers']) * 100 : 0;

        $this->response(['value' => round($rate, 2)]);
    }

    public function getClv() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Get average CLV over time period (daily)
        $query = "SELECT 
                    DATE(order_time) as date,
                    ROUND(AVG(order_total), 2) as clv
                  FROM orders
                  WHERE store_id = $store_id
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
}
