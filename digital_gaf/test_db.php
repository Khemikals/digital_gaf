<?php
include 'db_connect.php';

// Try to insert a fake person
$sql = "INSERT INTO applicants (first_name, last_name) VALUES ('TEST', 'USER')";

if ($conn->query($sql) === TRUE) {
    echo "<h1>✅ SUCCESS: Database Works!</h1>";
    echo "<p>New ID: " . $conn->insert_id . "</p>";
} else {
    echo "<h1>❌ ERROR: Database Broken.</h1>";
    echo "<p>" . $conn->error . "</p>";
}
?>