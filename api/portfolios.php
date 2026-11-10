<?php
require 'storage.php';

// GET api/portfolios.php -> all Portfolios (with their Items)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    sendJson(readPortfolios());
}

// POST api/portfolios.php -> create ONE new Portfolio
// fetch() sends the form fields with FormData, so PHP reads them from $_POST
$name = trim($_POST['displayName'] ?? '');

if ($name === '') {
    sendJson(['error' => 'Display name is required'], 400);
}

$portfolio = [
    'id'                 => uniqid('p'),
    'displayName'        => $name,
    'whatIEnjoy'         => trim($_POST['whatIEnjoy'] ?? ''),
    'whatIveTried'       => trim($_POST['whatIveTried'] ?? ''),
    'whatImCuriousAbout' => trim($_POST['whatImCuriousAbout'] ?? ''),
    'items'              => [],
];

$portfolios = readPortfolios();
$portfolios[] = $portfolio;          // add to the end of the list
writePortfolios($portfolios);

sendJson($portfolio, 201);           // 201 = Created
