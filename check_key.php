<?php
$dataFile = 'data.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $prompt = $input['prompt'] ?? null;

    if (!$prompt) {
        echo json_encode(['message' => 'Invalid input. Please provide a prompt.']);
        exit;
    }

    // Convert prompt to lowercase for case-insensitive comparison
    $promptLower = strtolower($prompt);

    // Load existing data
    $data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : ['knowledge' => []];

    // Convert all existing keys to lowercase for comparison
    $exists = false;
    foreach ($data['knowledge'] as $key => $value) {
        if (strtolower($key) === $promptLower) {
            $exists = true;
            break;
        }
    }

    echo json_encode(['exists' => $exists]);
}
?>
