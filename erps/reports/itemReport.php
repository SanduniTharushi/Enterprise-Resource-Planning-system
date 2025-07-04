<?php
include('../includes/header.php');
include '../config/db.php';

// Pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Filters
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$subcategory_id = isset($_GET['subcategory']) ? (int)$_GET['subcategory'] : 0;

// Base query
$query = "SELECT SQL_CALC_FOUND_ROWS i.item_name, ic.category, isc.sub_category, SUM(i.quantity) as total_quantity
          FROM item i
          JOIN item_category ic ON i.item_category = ic.id
          JOIN item_subcategory isc ON i.item_subcategory = isc.id
          WHERE 1=1";

// Add filters if selected
if($category_id > 0) {
    $query .= " AND i.item_category = $category_id";
}
if($subcategory_id > 0) {
    $query .= " AND i.item_subcategory = $subcategory_id";
}

$query .= " GROUP BY i.item_name, ic.category, isc.sub_category
            ORDER BY i.item_name
            LIMIT $start, $per_page";

$items = $conn->query($query);
$total = $conn->query("SELECT FOUND_ROWS() as total")->fetch_assoc()['total'];
$pages = ceil($total / $per_page);

// Get dropdown data
$categories = $conn->query("SELECT * FROM item_category ORDER BY category");
$subcategories = $conn->query("SELECT * FROM item_subcategory ORDER BY sub_category");
?>
<div class="container">

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Item Report</h2>
            <div>
                <a href="exportItemReport.php?category=<?= $category_id ?>&subcategory=<?= $subcategory_id ?>" 
                   class="btn btn-success">
                   <i class="fas fa-file-excel"></i> Export to Excel
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="get" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label>Category</label>
                    <select name="category" class="form-control" id="categorySelect">
                        <option value="0">All Categories</option>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Subcategory</label>
                    <select name="subcategory" class="form-control" id="subcategorySelect">
                        <option value="0">All Subcategories</option>
                        <?php while($sub = $subcategories->fetch_assoc()): ?>
                            <option value="<?= $sub['id'] ?>" <?= $sub['id'] == $subcategory_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sub['sub_category']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="itemReport.php" class="btn btn-outline-secondary">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Item Report Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Total Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($items->num_rows > 0): ?>
                        <?php while($item = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= htmlspecialchars($item['category']) ?></td>
                            <td><?= htmlspecialchars($item['sub_category']) ?></td>
                            <td><?= $item['total_quantity'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No items found for selected criteria</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
       </div>
        <!-- Pagination -->
        <?php if($pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page-1 ?>&category=<?= $category_id ?>&subcategory=<?= $subcategory_id ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&category=<?= $category_id ?>&subcategory=<?= $subcategory_id ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if($page < $pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page+1 ?>&category=<?= $category_id ?>&subcategory=<?= $subcategory_id ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script>
// Optional: Add JavaScript to filter subcategories based on category selection
document.getElementById('categorySelect').addEventListener('change', function() {
    // You could implement AJAX to load relevant subcategories here
});
</script>

<?php include('../includes/footer.php'); ?>