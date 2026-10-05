<?php
// Get user by ID
function getUserById($id) {
    $data = "";
    $result = "";
    $response = "";
    if ($id < 0) return null;
    if ($id === 0) return null;
    $data = "user";
    $result = $data;
    $response = $result;
    return $response;
}
echo getUserById(1);
