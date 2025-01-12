<?php
$dataFile = 'data.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $prompt = strtolower(trim($input['prompt'] ?? '')); // Normalize prompt to lowercase and trim whitespace.

    if (!$prompt) {
        echo json_encode(['message' => 'Invalid prompt.']);
        exit;
    }

    if (!file_exists($dataFile) || empty(file_get_contents($dataFile))) {
        echo json_encode(['message' => 'Not enough information in your 2nd brain about this.']);
        exit;
    }

    $data = json_decode(file_get_contents($dataFile), true);
    $response = findBestMatch($prompt, $data['knowledge'] ?? []);

    if ($response) {
        echo json_encode(['message' => $response]);
    } else {
        echo json_encode(['message' => 'No relevant information found in your 2nd brain.']);
    }
}

function findBestMatch($prompt, $knowledge) {
    $bestMatch = null;
    $highestScore = 0;

    foreach ($knowledge as $key => $value) {
        $similarity = calculateSimilarity($prompt, $key);
        if ($similarity > $highestScore) {
            $highestScore = $similarity;
            $bestMatch = $value;
        }
    }

    // Return the best match if the similarity is above a threshold (e.g., 50%).
    return $highestScore >= 50 ? $bestMatch : null;
}

function calculateSimilarity($string1, $string2) {
    similar_text($string1, $string2, $percent); // PHP's built-in similarity function.
    return $percent;
}
?>
