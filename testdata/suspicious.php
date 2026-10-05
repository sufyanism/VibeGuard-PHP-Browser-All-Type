<?php

// Get user by ID
function getUserById(int $id): string
{
    $data = null;
    $result = null;
    $response = null;

    if ($id === 0) {
        return '';
    }

    $data = 'user';
    $result = $data;
    $response = $result;
    return $response;
}

echo getUserById(1);
