<?php
// Shared helpers for all API files: require 'storage.php';

// Where the data files live:
// - on your laptop (php -S localhost:8000): the folder local-data/ in your project
//   (local-data/ is in .gitignore, so it is never pushed to GitHub)
// - on EC2 (Apache): /var/www/data, OUTSIDE the public web folder /var/www/html
if (php_sapi_name() === 'cli-server') {
    define('DATA_DIR', __DIR__ . '/../local-data');
    if (!is_dir(DATA_DIR)) {
        @mkdir(DATA_DIR);                      // create it the first time
    }
} else {
    define('DATA_DIR', '/var/www/data');
}

define('DATA_FILE', DATA_DIR . '/portfolios.json');

// Only ONE request at a time may use the data files.
// The lock is taken on the first read and released when the request ends,
// so "read -> add one thing -> write" can never be mixed up with another request.
function lockData() {
    static $lock = null;                       // keep it for the whole request
    if ($lock === null) {
        // @ = no PHP warning on the page; we send our own JSON error instead
        $lock = @fopen(DATA_DIR . '/data.lock', 'c');
        if ($lock === false) {
            cannotSave(DATA_DIR . '/data.lock');
        }
        flock($lock, LOCK_EX);                 // wait here until it is our turn
    }
}

// Read all Portfolios from the JSON file (empty list if no file yet)
function readPortfolios() {
    lockData();
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    return json_decode(file_get_contents(DATA_FILE), true) ?? [];
}

// Write all Portfolios back to the JSON file
function writePortfolios($portfolios) {
    $json = json_encode($portfolios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    // false = the file could not be written
    if (@file_put_contents(DATA_FILE, $json, LOCK_EX) === false) {
        cannotSave(DATA_FILE);
    }
}

// Writing failed: tell the student how to fix it
function cannotSave($file) {
    if (php_sapi_name() === 'cli-server') {
        // laptop: usually this storage.php is an old copy, or the folder is read-only
        $fix = 'On your laptop: make sure api/storage.php is the newest one from the starter, '
             . 'and that your project folder is not read-only.';
    } else {
        // EC2: usually /var/www/data is missing or not owned by www-data
        $fix = 'On EC2 run: sudo mkdir -p /var/www/data && sudo chown www-data:www-data /var/www/data';
    }
    sendJson(['error' => 'Server cannot save to ' . $file . '. ' . $fix], 500);
}

// Send an answer back to JavaScript as JSON, then stop
function sendJson($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
