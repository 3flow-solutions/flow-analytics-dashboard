<?php

namespace App\Http\Controllers;

use App\Database;

class SummaryController extends BaseController {

    public function getRevenue() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Get total paid revenue (FlowPromo + regular orders)
        $query = "SELECT ROUND(SUM(order_total), 2) as value FROM orders 
                  WHERE store_id = $store_id 
                  AND (payment_type LIKE '%Stripe%' OR payment_file != '')
                  AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $this->response(['value' => $data['value'] ?? 0]);
    }

    public function getRepeatCustomers() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Count customers with multiple orders
        $query = "SELECT COUNT(DISTINCT user_id) as value FROM (
                    SELECT user_id FROM orders
                    WHERE store_id = $store_id
                    AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'
                    GROUP BY user_id
                    HAVING COUNT(*) > 1
                  ) as repeat_customers";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $this->response(['value' => $data['value'] ?? 0]);
    }

    public function getReferralSales() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Get total referral revenue
        $query = "SELECT ROUND(SUM(order_total), 2) as value FROM orders 
                  WHERE store_id = $store_id 
                  AND referred_by IS NOT NULL 
                  AND referred_by != ''
                  AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $this->response(['value' => $data['value'] ?? 0]);
    }

    public function getPredictabilityScore() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        // Calculate weighted score:
        // 40% = Redemption Rate (FlowPromo)
        // 30% = Repeat Purchase Rate (FlowLoyalty)
        // 30% = Referral Conversion Rate (FlowLink)

        // 1. Redemption Rate
        $query1 = "SELECT 
                    COUNT(DISTINCT CASE WHEN redeemed_at IS NOT NULL THEN order_id END) as redeemed,
                    COUNT(DISTINCT CASE WHEN (payment_type LIKE '%Stripe%' OR payment_file != '') THEN order_id END) as paid
                  FROM orders
                  WHERE store_id = $store_id
                  AND FlowPromo = 1
                  AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'";
        $result1 = Database::query($query1);
        $data1 = Database::fetchAssoc($result1);
        $redemption_rate = $data1['paid'] > 0 ? ($data1['redeemed'] / $data1['paid']) * 100 : 0;

        // 2. Repeat Purchase Rate
        $query2 = "SELECT 
                    COUNT(DISTINCT user_id) as total,
                    COUNT(DISTINCT CASE WHEN order_count > 1 THEN user_id END) as repeat
                  FROM (
                    SELECT user_id, COUNT(*) as order_count
                    FROM orders
                    WHERE store_id = $store_id
                    AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'
                    GROUP BY user_id
                  ) as customer_orders";
        $result2 = Database::query($query2);
        $data2 = Database::fetchAssoc($result2);
        $repeat_rate = $data2['total'] > 0 ? ($data2['repeat'] / $data2['total']) * 100 : 0;

        // 3. Referral Conversion Rate
        $query3 = "SELECT 
                    COUNT(DISTINCT CASE WHEN referred_by IS NOT NULL AND referred_by != '' THEN order_id END) as referral_conversions,
                    COUNT(DISTINCT order_id) as total_orders
                  FROM orders
                  WHERE store_id = $store_id
                  AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'";
        $result3 = Database::query($query3);
        $data3 = Database::fetchAssoc($result3);
        $referral_rate = $data3['total_orders'] > 0 ? ($data3['referral_conversions'] / $data3['total_orders']) * 100 : 0;

        // Calculate weighted score
        $score = ($redemption_rate * 0.4) + ($repeat_rate * 0.3) + ($referral_rate * 0.3);

        $this->response(['value' => round($score, 2)]);
    }
}
