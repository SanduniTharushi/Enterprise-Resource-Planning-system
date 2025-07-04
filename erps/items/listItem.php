
<?php
include('../includes/header.php');
include '../config/db.php';

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where = "WHERE i.item_name LIKE '%$search%' 
              OR i.item_code LIKE '%$search%' 
              OR c.category LIKE '%$search%' 
              OR s.sub_category LIKE '%$search%'";
}

$items = $conn->query("SELECT i.*, c.category, s.sub_category 
                      FROM item i
                      JOIN item_category c ON i.item_category = c.id
                      JOIN item_subcategory s ON i.item_subcategory = s.id
                      $where
                      ORDER BY i.id DESC");
?>
<div class="container">
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Item List</h2>
            <a href="addItem.php" class="btn btn-primary">+ Add New Item</a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form method="get" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by name, code or category" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-success" type="submit">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="listItem.php" class="btn btn-outline-danger">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Items Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($items->num_rows > 0): ?>
                        <?php while($item = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= htmlspecialchars($item['item_code']) ?></td>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= htmlspecialchars($item['category']) ?></td>
                            <td><?= htmlspecialchars($item['sub_category']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($item['unit_price'], 2) ?></td>
                            <td>
                                <a href="editItem.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="deleteItem.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<?php include('../includes/footer.php'); ?>
