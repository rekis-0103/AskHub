<?php
$password1 = "admin123";
$password2 = "user123";

$hash1 = password_hash($password1, PASSWORD_BCRYPT, ['cost' => 12]);
$hash2 = password_hash($password2, PASSWORD_BCRYPT, ['cost' => 12]);

echo "admin123: " . $hash1 . PHP_EOL;
echo "user123: " . $hash2 . PHP_EOL;