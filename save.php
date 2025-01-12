<?php
$dataFile = 'data.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $prompt = $input['prompt'] ?? null;
    $answer = $input['answer'] ?? null;
    $action = $input['action'] ?? 'overwrite'; // Default to overwrite if no action specified

    if (!$prompt || !$answer) {
        echo json_encode(['message' => 'Invalid input. Please provide both prompt and answer.']);
        exit;
    }

    // Convert prompt to lowercase for case-insensitive comparison
    $promptLower = strtolower($prompt);

    // Load existing data
    $data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : ['knowledge' => []];

    // Find if the key exists in any case
    $existingKey = null;
    foreach ($data['knowledge'] as $key => $value) {
        if (strtolower($key) === $promptLower) {
            $existingKey = $key;
            break;
        }
    }

    // Handle the answer based on action
    if ($existingKey !== null) {
        if ($action === 'add') {
            // Append the new answer with a newline
            $data['knowledge'][$existingKey] .= "\n" . $answer;
        } else {
            // Overwrite using the new prompt case but keep the same key
            $oldValue = $data['knowledge'][$existingKey];
            unset($data['knowledge'][$existingKey]);
            $data['knowledge'][$prompt] = $answer;
        }
    } else {
        // New entry
        $data['knowledge'][$prompt] = $answer;
    }

    // Save back to the file
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode(['message' => 'Prompt and answer saved successfully.']);
}
?>
