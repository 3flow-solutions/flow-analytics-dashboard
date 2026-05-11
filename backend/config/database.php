<?php

// 🔱 SESSION PERSISTENCE LAYER (from your existing setup)
ini_set('session.cookie_domain', '.flowcart.store');
session_start();

date_default_timezone_set("Asia/Singapore");

// Database Connection (your existing credentials)
$con = mysqli_connect(
    "localhost",
    "flowcart_storeuser",
    "9KF5-Q4DgFrW",
    "flowcart_store"
);

if (!$con) {
    die(json_encode(['error' => 'Database connection failed']));
}

// Set charset
mysqli_set_charset($con, "utf8mb4");

// 🔱 TRIDENT SUBDOMAIN DETECTION ENGINE (from your existing setup)
function getStoreUrl() {
    $current_host = $_SERVER['HTTP_HOST'];
    $host_parts = explode('.', $current_host);

    // Check if we are using a subdomain (e.g., brand.flowcart.store)
    if (count($host_parts) >= 3 && strpos($host_parts[count($host_parts)-2], 'flowcart') !== false) {
        $store_url = $host_parts[0];
    } else {
        $store_url = isset($_GET['store_url']) ? $_GET['store_url'] : '';
        $store_url = isset($_GET['store']) ? $_GET['store'] : $store_url; // Also check 'store' param
    }

    // EXCLUSION LAYER
    if (in_array($store_url, ['www', 'test', 'mail', 'webmail', 'admin', 'api'])) {
        $store_url = isset($_GET['store_url']) ? $_GET['store_url'] : '';
    }

    return $store_url;
}

// 🔱 AUTOMATIC STORE INITIALIZATION
function initializeStore($con) {
    global $_SESSION;
    $store_url = getStoreUrl();

    if (!empty($store_url)) {
        $store_url = mysqli_real_escape_string($con, $store_url);
        $store_check = mysqli_query($con, "SELECT store_id, store_currency, store_color FROM store WHERE store_url = '$store_url'");
        
        if ($st_row = mysqli_fetch_array($store_check)) {
            $_SESSION["currency"] = $st_row["store_currency"];
            $_SESSION["store_color"] = $st_row["store_color"];
            $_SESSION["store_url"] = $store_url;
            $_SESSION["store_id"] = $st_row["store_id"];
            return $st_row["store_id"];
        }
    }

    return null;
}

// Initialize store on every request
$_SESSION["store_id"] = initializeStore($con);
$_SESSION["store_url"] = getStoreUrl();

return [
    'connection' => $con,
    'store_id' => $_SESSION["store_id"] ?? null,
    'store_url' => $_SESSION["store_url"] ?? null,
];
