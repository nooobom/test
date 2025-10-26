<?php
$to = "omdalvi4205@gmail.com";
$subject = "Test email from InfinityFree";
$message = "If you see this, mail() works!";
$headers = "From: projectsem5year2025@gmail.com\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "✅ Mail sent!";
} else {
    echo "❌ Mail failed.";
}
?>
