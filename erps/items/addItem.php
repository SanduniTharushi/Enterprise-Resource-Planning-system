<?php
ob_start();
include('../includes/header.php');
include '../config/db.php';

$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_code = $_POST['item_code'];
    $item_category = $_POST['item_category'];
    $item_subcategory = $_POST['item_subcategory'];
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];

    $sql = "INSERT INTO item (item_code, item_category, item_subcategory, item_name, quantity, unit_price) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("siisid", $item_code, $item_category, $item_subcategory, $item_name, $quantity, $unit_price);
        if ($stmt->execute()) {
            header("Location: listItem.php?message=added");
            exit();
        } else {
            $errorMsg = "Error inserting item: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "Prepare failed: " . $conn->error;
    }
}

// Fetch categories and subcategories for dropdowns
$categories = $conn->query("SELECT * FROM item_category");
$subcategories = $conn->query("SELECT * FROM item_subcategory");
?>

<div class="container mt-4">
    <div class="card-header text-white">
        <h5><i class="fas fa-plus-circle me-2"></i>Add New Item</h5>
    </div>
    <div class="card-body">
        <?php if ($errorMsg): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Item Code</label>
                <input type="text" class="form-control" name="item_code" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="item_category" required>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Subcategory</label>
                <select class="form-select" name="item_subcategory" required>
                    <?php while($sub = $subcategories->fetch_assoc()): ?>
                        <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['sub_category']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Item Name</label>
                <input type="text" class="form-control" name="item_name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" name="quantity" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Unit Price</label>
                <input type="number" step="0.01" class="form-control" name="unit_price" required>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Add Item</button>
        </form>
    </div>
</div>

<?php 
include('../includes/footer.php');
ob_end_flush();
?>
