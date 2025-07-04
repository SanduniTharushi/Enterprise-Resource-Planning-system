<?php
ob_start();
include('../includes/header.php');
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $item_code = $_POST['item_code'];
    $item_category = $_POST['item_category'];
    $item_subcategory = $_POST['item_subcategory'];
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];

    $sql = "UPDATE item SET 
            item_code = ?, 
            item_category = ?, 
            item_subcategory = ?, 
            item_name = ?, 
            quantity = ?, 
            unit_price = ? 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisidi", $item_code, $item_category, $item_subcategory, $item_name, $quantity, $unit_price, $id);

    if ($stmt->execute()) {
        header("Location: listItem.php?message=updated");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

$id = $_GET['id'] ?? 0;
$item = $conn->query("SELECT * FROM item WHERE id = $id")->fetch_assoc();

if (!$item) {
    echo "<div class='alert alert-danger'>Item not found!</div>";
    include('../includes/footer.php');
    exit();
}

$categories = $conn->query("SELECT * FROM item_category");
$subcategories = $conn->query("SELECT * FROM item_subcategory");
?>

<div class="card">
    <div class="card-header">
        <h2>Edit Item</h2>
    </div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
            <div class="mb-3">
                <label class="form-label">Item Code</label>
                <input type="text" class="form-control" name="item_code" value="<?= htmlspecialchars($item['item_code']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="item_category" required>
                    <?php 
                    $categories->data_seek(0);
                    while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $item['item_category'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Subcategory</label>
                <select class="form-select" name="item_subcategory" required>
                    <?php 
                    $subcategories->data_seek(0);
                    while($sub = $subcategories->fetch_assoc()): ?>
                        <option value="<?= $sub['id'] ?>" <?= $sub['id'] == $item['item_subcategory'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sub['sub_category']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Item Name</label>
                <input type="text" class="form-control" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Unit Price</label>
                <input type="number" step="0.01" class="form-control" name="unit_price" value="<?= htmlspecialchars($item['unit_price']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Item</button>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); 
ob_end_flush();
?>
