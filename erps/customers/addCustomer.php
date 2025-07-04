<?php
ob_start();
include '../config/db.php';

$title = $first = $middle = $last = $contact = $district = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title    = trim($_POST['title']);
    $first    = trim($_POST['first_name']);
    $middle   = trim($_POST['middle_name']);
    $last     = trim($_POST['last_name']);
    $contact  = trim($_POST['contact_no']);
    $district = (int)$_POST['district']; // district id as int

    if (!$first || !$last || !$contact || !$district) {
        $errors[] = "Please fill in all required fields.";
    }

    if (!preg_match('/^\d{10}$/', $contact)) {
        $errors[] = "Contact number must be a 10-digit number.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO customer (title, first_name, middle_name, last_name, contact_no, district) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        // district as integer id
        $stmt->bind_param("sssssi", $title, $first, $middle, $last, $contact, $district);
        if ($stmt->execute()) {
            header("Location: listCustomer.php");
            exit();
        } else {
            $errors[] = "Insert failed: " . $stmt->error;
        }
    }
}

// Get districts for select dropdown
$districts = $conn->query("SELECT * FROM district WHERE active='yes'");

include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Add Customer</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <select name="title" id="title" class="form-select" required>
                <option value="Mr" <?= $title === "Mr" ? 'selected' : '' ?>>Mr</option>
                <option value="Mrs" <?= $title === "Mrs" ? 'selected' : '' ?>>Mrs</option>
                <option value="Miss" <?= $title === "Miss" ? 'selected' : '' ?>>Miss</option>
                <option value="Dr" <?= $title === "Dr" ? 'selected' : '' ?>>Dr</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="first_name" class="form-label">First Name *</label>
            <input type="text" name="first_name" id="first_name" class="form-control" required value="<?= htmlspecialchars($first) ?>">
        </div>

        <div class="mb-3">
            <label for="middle_name" class="form-label">Middle Name</label>
            <input type="text" name="middle_name" id="middle_name" class="form-control" value="<?= htmlspecialchars($middle) ?>">
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name *</label>
            <input type="text" name="last_name" id="last_name" class="form-control" required value="<?= htmlspecialchars($last) ?>">
        </div>

        <div class="mb-3">
            <label for="contact_no" class="form-label">Contact No *</label>
            <input type="text" name="contact_no" id="contact_no" class="form-control" required pattern="\d{10}" title="Enter a 10-digit number" value="<?= htmlspecialchars($contact) ?>">
        </div>

        <div class="mb-3">
            <label for="district" class="form-label">District *</label>
            <select name="district" id="district" class="form-select" required>
                <option value="" disabled <?= !$district ? 'selected' : '' ?>>Select District</option>
                <?php while ($d = $districts->fetch_assoc()): ?>
                    <option value="<?= (int)$d['id'] ?>" <?= $district == $d['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['district']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Add Customer</button>
    </form>
</div>

<?php
include '../includes/footer.php';
ob_end_flush();
?>
