<?php
ob_start();  // Start output buffering

include '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch customer first
$stmt = $conn->prepare("SELECT * FROM customer WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

// Redirect if customer not found
if (!$customer) {
    header("Location: listCustomer.php");
    exit();
}

// Fetch districts for dropdown
$districts = $conn->query("SELECT * FROM district WHERE active='yes'");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title    = $_POST['title'];
    $first    = $_POST['first_name'];
    $middle   = $_POST['middle_name'];
    $last     = $_POST['last_name'];
    $contact  = $_POST['contact_no'];
    $district = $_POST['district'];

    // Validation
    if (empty($first) || empty($last) || empty($contact) || empty($district)) {
        $errors[] = "Please fill in all required fields.";
    }

    if (!preg_match('/^\d{10}$/', $contact)) {
        $errors[] = "Contact number must be 10 digits.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE customer SET title=?, first_name=?, middle_name=?, last_name=?, contact_no=?, district=? WHERE id=?");
        $stmt->bind_param("ssssssi", $title, $first, $middle, $last, $contact, $district, $id);
        if ($stmt->execute()) {
            header("Location: listCustomer.php");
            exit();
        } else {
            $errors[] = "Update failed: " . $stmt->error;
        }
    }
}

include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Edit Customer</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label>Title</label>
            <select name="title" class="form-control" required>
                <option value="Mr" <?= $customer['title'] === 'Mr' ? 'selected' : '' ?>>Mr</option>
                <option value="Mrs" <?= $customer['title'] === 'Mrs' ? 'selected' : '' ?>>Mrs</option>
                <option value="Miss" <?= $customer['title'] === 'Miss' ? 'selected' : '' ?>>Miss</option>
                <option value="Dr" <?= $customer['title'] === 'Dr' ? 'selected' : '' ?>>Dr</option>
            </select>
        </div>

        <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($customer['first_name']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Middle Name</label>
            <input type="text" name="middle_name" value="<?= htmlspecialchars($customer['middle_name']) ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($customer['last_name']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Contact No</label>
            <input type="text" name="contact_no" value="<?= htmlspecialchars($customer['contact_no']) ?>" class="form-control" required pattern="\d{10}" title="10 digit number">
        </div>

        <div class="mb-3">
            <label>District</label>
            <select name="district" class="form-control" required>
                <?php while ($d = $districts->fetch_assoc()): ?>
                    <option value="<?= $d['id'] ?>" <?= $d['id'] == $customer['district'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['district']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Customer</button>
        <a href="listCustomer.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php
include '../includes/footer.php';
ob_end_flush();  // Flush the output buffer and send output
?>
