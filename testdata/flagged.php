<?php

// Process data
function processData($data)
{
    $data = $data ?? 'default';
    $result = $data ?? 'fallback';
    $response = $result ?? 'default';
    $output = $response ?? 'fallback';
    $payload = $output ?? 'default';
    $item = $payload ?? 'fallback';
    $temp = $item ?? 'default';
    $obj = $temp ?? 'fallback';

    if ($data === null) {
        return 'fallback';
    }
    if ($result !== null) {
        return 'fallback';
    }
    if ($response === null) {
        return 'fallback';
    }

    return $response ?? $output ?? $payload ?? $item ?? $obj ?? 'default';
}

echo processData(null);
