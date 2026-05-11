<?php

namespace App\Http\Controllers;

use App\Database;

class PromoMetricsController extends BaseController {

    public function getCampaigns() {
        $store_id = Database::getStoreId();
        
        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        $query = "SELECT id, offer_title FROM offers WHERE store_id = $store_id ORDER BY id DESC";
        $result = Database::query($query);
        $campaigns = [];

        while ($row = Database::fetchAssoc($result)) {
            $campaigns[] = $row;
        }

        $this->response(['data' => $campaigns]);
    }

    public function getClaimed() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $campaign_id = $_GET['campaign'] ?? null;
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        $where = "store_id = $store_id AND FlowPromo = 1";
        if ($campaign_id) {
            $where .= " AND offer_id = $campaign_id";
        }
        $where .= " AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'";

        $query = "SELECT COUNT(*) as value FROM orders WHERE $where";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $this->response($data);
    }

    public function getPaid() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $campaign_id = $_GET['campaign'] ?? null;
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        $where = "store_id = $store_id AND FlowPromo = 1 AND (payment_type LIKE '%Stripe%' OR payment_file != '')";
        if ($campaign_id) {
            $where .= " AND offer_id = $campaign_id";
        }
        $where .= " AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'";

        $query = "SELECT COUNT(*) as value FROM orders WHERE $where";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $this->response($data);
    }

    public function getRedeemed() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $campaign_id = $_GET['campaign'] ?? null;
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        $where = "store_id = $store_id AND FlowPromo = 1 AND redeemed_at IS NOT NULL";
        if ($campaign_id) {
            $where .= " AND offer_id = $campaign_id";
        }
        $where .= " AND DATE(redeemed_at) BETWEEN '$start_date' AND '$end_date'";

        $query = "SELECT COUNT(*) as value FROM orders WHERE $where";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $this->response($data);
    }

    public function getRedemptionRate() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $campaign_id = $_GET['campaign'] ?? null;
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        $where_paid = "store_id = $store_id AND FlowPromo = 1 AND (payment_type LIKE '%Stripe%' OR payment_file != '')";
        $where_redeemed = "store_id = $store_id AND FlowPromo = 1 AND redeemed_at IS NOT NULL";
        
        if ($campaign_id) {
            $where_paid .= " AND offer_id = $campaign_id";
            $where_redeemed .= " AND offer_id = $campaign_id";
        }
        
        $where_paid .= " AND DATE(order_time) BETWEEN '$start_date' AND '$end_date'";
        $where_redeemed .= " AND DATE(redeemed_at) BETWEEN '$start_date' AND '$end_date'";

        $query = "SELECT 
                    (SELECT COUNT(*) FROM orders WHERE $where_redeemed) as redeemed,
                    (SELECT COUNT(*) FROM orders WHERE $where_paid) as paid";
        $result = Database::query($query);
        $data = Database::fetchAssoc($result);

        $rate = $data['paid'] > 0 ? ($data['redeemed'] / $data['paid']) * 100 : 0;

        $this->response(['value' => round($rate, 2)]);
    }

    public function getSpeed() {
        $this->authenticate();
        $store_id = Database::getStoreId();
        $campaign_id = $_GET['campaign'] ?? null;
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');

        if (!$store_id) {
            $this->response(['error' => 'Store not found'], 404);
            return;
        }

        $where = "store_id = $store_id AND FlowPromo = 1 AND redeemed_at IS NOT NULL";
        if ($campaign_id) {
            $where .= " AND offer_id = $campaign_id";
        }
        $where .= " AND DATE(redeemed_at) BETWEEN '$start_date' AND '$end_date'";

        // Get daily sell-out speed
        $query = "SELECT 
                    DATE(redeemed_at) as date,
                    COUNT(*) as speed
                  FROM orders 
                  WHERE $where
                  GROUP BY DATE(redeemed_at)
                  ORDER BY date ASC";
        $result = Database::query($query);
        $data = [];

        while ($row = Database::fetchAssoc($result)) {
            $data[] = $row;
        }

        $this->response(['data' => $data]);
    }
}
