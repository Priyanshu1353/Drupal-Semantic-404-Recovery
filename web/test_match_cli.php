<?php

/**
 * @file
 * CLI test – verifies the AI engine returns a valid match for a test path.
 *
 * Usage:
 *   C:\php\php.exe web\test_match_cli.php
 *   C:\php\php.exe web\test_match_cli.php /crypto
 */

$path = $argv[1] ?? '/investment';
$url  = 'http://127.0.0.1:8000/match?path=' . urlencode($path);

echo PHP_EOL;
echo '--- Semantic 404 Matcher Test ---' . PHP_EOL;
echo 'Testing Path: ' . $path . PHP_EOL;
echo 'Requesting Match from AI Engine at ' . $url . ' ...' . PHP_EOL . PHP_EOL;

$context = stream_context_create(['http' => ['timeout' => 5]]);
$body    = @file_get_contents($url, false, $context);

if ($body === false) {
    echo '[ERROR] Could not reach the AI Engine.' . PHP_EOL;
    echo 'Make sure the FastAPI server is running on port 8000.' . PHP_EOL;
    exit(1);
}

$data = json_decode($body, true);

if (!isset($data['score'])) {
    echo '[ERROR] Unexpected response: ' . $body . PHP_EOL;
    exit(1);
}

if ($data['score'] >= 0.75) {
    echo '[MATCH FOUND!]' . PHP_EOL;
    echo 'Title:    ' . $data['title']   . PHP_EOL;
    echo 'URL:      ' . $data['url']     . PHP_EOL;
    echo 'Snippet:  ' . $data['snippet'] . PHP_EOL;
    printf('Score:    %.2f (CONFIDENCE)' . PHP_EOL, $data['score']);
    echo PHP_EOL . '--- SUCCESS ---' . PHP_EOL;
} else {
    printf('[LOW CONFIDENCE] Score %.2f – no suggestion shown.' . PHP_EOL, $data['score']);
    echo '--- NO MATCH ---' . PHP_EOL;
}

echo PHP_EOL;
