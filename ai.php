<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $prompt = $input['prompt'] ?? '';

    if (empty($prompt)) {
        echo json_encode(['error' => 'Please provide a prompt.']);
        exit;
    }

    $apiKey = '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';
    $url = 'https://api.groq.com/openai/v1/chat/completions';

    $data = [
        'model' => 'llama-3.3-70b-versatile',
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ]
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    curl_close($ch);

    if ($error) {
        echo json_encode(['error' => 'Connection error: ' . $error]);
        exit;
    }

    if ($httpCode !== 200) {
        echo json_encode(['error' => 'API error: ' . $response]);
        exit;
    }

    $result = json_decode($response, true);

    if (isset($result['choices'][0]['message']['content'])) {
        echo json_encode(['response' => $result['choices'][0]['message']['content']]);
    } else {
        echo json_encode(['error' => 'Unexpected API response format']);
    }
}
?>
