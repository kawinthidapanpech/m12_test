<?php
require 'storage.php';

// POST api/items.php -> add ONE Item to ONE Portfolio
// fetch() sends the form fields with FormData, so PHP reads them from $_POST
$title = trim($_POST['title'] ?? '');

if ($title === '') {
    sendJson(['error' => 'Title is required'], 400);
}
// Module 14: check every field (date, URL, ...) here as well

// Build the Item in the shape of the Data Contract
$item = [
    'id'          => uniqid('item'),
    'title'       => $title,
    'type'        => trim($_POST['type'] ?? ''),
    'date'        => trim($_POST['date'] ?? ''),
    'role'        => trim($_POST['role'] ?? ''),
    'description' => trim($_POST['description'] ?? ''),
    'skills'      => array_map('trim', explode(',', $_POST['skills'] ?? '')),   // "HTML, CSS" -> ["HTML", "CSS"]
    'reflection'  => [
        'whatIDid'     => trim($_POST['whatIDid'] ?? ''),
        'whatILearned' => trim($_POST['whatILearned'] ?? ''),
        'challenge'    => trim($_POST['challenge'] ?? ''),
        'nextStep'     => trim($_POST['nextStep'] ?? ''),
    ],
    'evidence'    => [trim($_POST['evidence'] ?? '')],                          // one link -> a list with one link
];

$portfolios = readPortfolios();

foreach ($portfolios as $i => $portfolio) {
    if ($portfolio['id'] === ($_POST['portfolioId'] ?? '')) {
        $portfolios[$i]['items'][] = $item;   // add the Item to THIS Portfolio
        writePortfolios($portfolios);
        sendJson($item, 201);
    }
}

sendJson(['error' => 'Portfolio not found'], 404);
