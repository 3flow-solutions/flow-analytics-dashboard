<?php

require_once __DIR__ . '/../app/Http/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Http/Controllers/PromoMetricsController.php';
require_once __DIR__ . '/../app/Http/Controllers/LoyaltyMetricsController.php';
require_once __DIR__ . '/../app/Http/Controllers/ReferralMetricsController.php';
require_once __DIR__ . '/../app/Http/Controllers/SummaryController.php';
require_once __DIR__ . '/../app/Http/Controllers/ExportController.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path);

// CORS Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Routes
switch (true) {
    // Auth Routes
    case $path === '/auth/login' && $method === 'POST':
        (new \App\Http\Controllers\AuthController())->login();
        break;
    case $path === '/auth/logout' && $method === 'POST':
        (new \App\Http\Controllers\AuthController())->logout();
        break;
    case $path === '/auth/me' && $method === 'GET':
        (new \App\Http\Controllers\AuthController())->me();
        break;

    // Campaigns
    case $path === '/campaigns' && $method === 'GET':
        (new \App\Http\Controllers\PromoMetricsController())->getCampaigns();
        break;

    // FlowPromo Routes
    case $path === '/promos/claimed' && $method === 'GET':
        (new \App\Http\Controllers\PromoMetricsController())->getClaimed();
        break;
    case $path === '/promos/paid' && $method === 'GET':
        (new \App\Http\Controllers\PromoMetricsController())->getPaid();
        break;
    case $path === '/promos/redeemed' && $method === 'GET':
        (new \App\Http\Controllers\PromoMetricsController())->getRedeemed();
        break;
    case $path === '/promos/redemption-rate' && $method === 'GET':
        (new \App\Http\Controllers\PromoMetricsController())->getRedemptionRate();
        break;
    case $path === '/promos/speed' && $method === 'GET':
        (new \App\Http\Controllers\PromoMetricsController())->getSpeed();
        break;

    // FlowLoyalty Routes
    case $path === '/loyalty/stamps' && $method === 'GET':
        (new \App\Http\Controllers\LoyaltyMetricsController())->getStamps();
        break;
    case $path === '/loyalty/rewards' && $method === 'GET':
        (new \App\Http\Controllers\LoyaltyMetricsController())->getRewards();
        break;
    case $path === '/loyalty/repeat-rate' && $method === 'GET':
        (new \App\Http\Controllers\LoyaltyMetricsController())->getRepeatRate();
        break;
    case $path === '/loyalty/clv' && $method === 'GET':
        (new \App\Http\Controllers\LoyaltyMetricsController())->getClv();
        break;

    // FlowLink Routes
    case $path === '/referrals/links' && $method === 'GET':
        (new \App\Http\Controllers\ReferralMetricsController())->getLinks();
        break;
    case $path === '/referrals/conversions' && $method === 'GET':
        (new \App\Http\Controllers\ReferralMetricsController())->getConversions();
        break;
    case $path === '/referrals/revenue' && $method === 'GET':
        (new \App\Http\Controllers\ReferralMetricsController())->getRevenue();
        break;
    case $path === '/referrals/top' && $method === 'GET':
        (new \App\Http\Controllers\ReferralMetricsController())->getTopReferrers();
        break;

    // Summary Routes
    case $path === '/summary/revenue' && $method === 'GET':
        (new \App\Http\Controllers\SummaryController())->getRevenue();
        break;
    case $path === '/summary/repeat' && $method === 'GET':
        (new \App\Http\Controllers\SummaryController())->getRepeatCustomers();
        break;
    case $path === '/summary/referrals' && $method === 'GET':
        (new \App\Http\Controllers\SummaryController())->getReferralSales();
        break;
    case $path === '/summary/predictability' && $method === 'GET':
        (new \App\Http\Controllers\SummaryController())->getPredictabilityScore();
        break;

    // Export Routes
    case $path === '/export' && $method === 'GET':
        (new \App\Http\Controllers\ExportController())->export();
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
        break;
}
