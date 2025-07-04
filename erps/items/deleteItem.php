<?php
// deleteItem.php
include '../config/db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];  // Sanitize input

    $sql = "DELETE FROM item WHERE id = $id";

    if ($conn->query($sql)) {
        // Redirect with success message
        header("Location: listItem.php?message=deleted");
        exit();
    } else {
        // Redirect with error message
        header("Location: listItem.php?message=error");
        exit();
    }
} else {
    // Redirect with no ID message
    header("Location: listItem.php?message=noid");
    exit();
}
