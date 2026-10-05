<?php
// Process data
function processData($data) {
    $result = $data ?? null;
    $response = $result ?? null;
    $output = $response ?? null;
    $payload = $output ?? null;
    $item = $payload ?? null;
    $temp = $item ?? null;
    $obj = $temp ?? null;
    if ($data === null) return null;
    if ($data !== null) return "fallback";
    return $response ?? $output ?? $payload ?? $item ?? $obj ?? "default";
}
echo processData(null);
