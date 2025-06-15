<?php
session_start();
include 'config.php';

$personID = isset($_GET['personID']) ? intval($_GET['personID']) : 0;

if ($personID <= 0) {
    echo "Invalid person ID.";
    exit;
}

// Delete from related tables first to avoid foreign key constraint issues
$conn->begin_transaction();

try {
    // Delete from education
    $stmt = $conn->prepare("DELETE FROM education WHERE personID = ?");
    $stmt->bind_param("i", $personID);
    $stmt->execute();

    // Delete from occupant
    $stmt = $conn->prepare("DELETE FROM occupant WHERE personID = ?");
    $stmt->bind_param("i", $personID);
    $stmt->execute();

    // Finally, delete from person
    $stmt = $conn->prepare("DELETE FROM person WHERE personID = ?");
    $stmt->bind_param("i", $personID);
    $stmt->execute();

    $conn->commit();

    // Redirect to dashboard or confirmation page
    header("Location: Dashboard.php?delete=success");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    echo "Error deleting person: " . $e->getMessage();
}
?>
