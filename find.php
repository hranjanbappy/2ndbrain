<?php
$dataFile = 'data.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $searchTerms = strtolower(trim($input['prompt'] ?? '')); // Normalize search terms to lowercase

    if (!$searchTerms) {
        echo json_encode(['message' => 'Please provide search terms.']);
        exit;
    }

    if (!file_exists($dataFile)) {
        echo json_encode(['message' => 'No data found.', 'results' => []]);
        exit;
    }

    $data = json_decode(file_get_contents($dataFile), true);
    $results = [];

    // Split search terms into words
    $searchWords = preg_split('/\s+/', $searchTerms);

    foreach ($data['knowledge'] as $prompt => $answer) {
        $promptLower = strtolower($prompt);
        $answerLower = strtolower($answer);
        $matched = false;
        $matchScore = 0;

        // Check each search word
        foreach ($searchWords as $word) {
            // Check in prompt
            if (strpos($promptLower, $word) !== false) {
                $matched = true;
                $matchScore++;
            }
            // Check in answer
            if (strpos($answerLower, $word) !== false) {
                $matched = true;
                $matchScore++;
            }
        }

        if ($matched) {
            $results[] = [
                'prompt' => $prompt,
                'answer' => $answer,
                'score' => $matchScore
            ];
        }
    }

    // Sort results by number of matches (higher score first)
    usort($results, function($a, $b) {
        return $b['score'] - $a['score'];
    });

    // Remove the score before sending the response
    $results = array_map(function($item) {
        unset($item['score']);
        return $item;
    }, $results);

    echo json_encode(['results' => $results]);
}
?>
