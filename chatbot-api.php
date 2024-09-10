<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Load API Key (add your Hugging Face API key here)


// Get the user message from the request
$request = json_decode(file_get_contents('php://input'), true);
$userMessage = $request['message'] ?? '';

// Prepare the data for the API request
$data = json_encode([
    "inputs" => $userMessage,
    "options" => [
        "wait_for_model" => true
    ]
]);

// API request to Hugging Face
$ch = curl_init('https://api-inference.huggingface.co/models/gpt2'); // Change model if needed
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);

// Error handling for cURL
if (curl_errno($ch)) {
    $botMessage = 'Request Error: ' . curl_error($ch);
} else {
    $responseData = json_decode($response, true);
    // Log the raw response for debugging
    file_put_contents('response_log.txt', print_r($responseData, true));

    if (isset($responseData[0]['generated_text'])) {
        $botMessage = $responseData[0]['generated_text'];
    } else {
        $botMessage = 'API Error: ' . json_encode($responseData);
    }
}

curl_close($ch);

// Return response to the frontend
echo json_encode(['response' => $botMessage]);
?>
