<?php
$inputPassword = "admin123";
$hashFromDB = '$2y$12$...'; 

if (password_verify($inputPassword, $hashFromDB)) {
    echo "Password benar";
} else {
    echo "Password salah";
}