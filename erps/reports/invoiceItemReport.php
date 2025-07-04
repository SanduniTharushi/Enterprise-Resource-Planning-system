<?php
include('../includes/header.php');
include '../config/db.php';

// Pagination
$per_page = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Filters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$customer_id = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;
$item_id = isset($_GET['item']) ? (int)$_GET['item'] : 0;

// Base query
$query = "SELECT SQL_CALC_FOUND_ROWS im.*, i.date, c.title, c.first_name, c.last_name, 
          it.item_name, it.item_code, ic.category, it.unit_price
          FROM invoice_master im
          JOIN invoice i ON im.invoice_no = i.invoice_no
          JOIN customer c ON i.customer = c.id
          JOIN item it ON im.item_id = it.id
          JOIN item_category ic ON it.item_category = ic.id
          WHERE i.date BETWEEN '$start_date' AND '$end_date'";

// Add filters if selected
if($customer_id > 0) {
    $query .= " AND i.customer = $customer_id";
}
if($item_id > 0) {
    $query .= " AND im.item_id = $item_id";
}

$query .= " ORDER BY i.date DESC, im.invoice_no LIMIT $start, $per_page";

$invoice_items = $conn->query($query);
$total = $conn->query("SELECT FOUND_ROWS() as total")->fetch_assoc()['total'];
$pages = ceil($total / $per_page);

// Get dropdown data
$customers = $conn->query("SELECT id, CONCAT(title, ' ', first_name, ' ', last_name) as name FROM customer ORDER BY first_name");
$items = $conn->query("SELECT id, CONCAT(item_name, ' (', item_code, ')') as name FROM item ORDER BY item_name");
?>
<div class="container">

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Invoice Item Report</h2>
            <div>
                <a href="exportInvoiceItemReport.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&customer=<?= $customer_id ?>&item=<?= $item_id ?>" 
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
                <div class="col-md-2">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                <div class="col-md-2">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                <div class="col-md-3">
                    <label>Customer</label>
                    <select name="customer" class="form-control">
                        <option value="0">All Customers</option>
                        <?php while($cust = $customers->fetch_assoc()): ?>
                            <option value="<?= $cust['id'] ?>" <?= $cust['id'] == $customer_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cust['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Item</label>
                    <select name="item" class="form-control">
                        <option value="0">All Items</option>
                        <?php while($itm = $items->fetch_assoc()): ?>
                            <option value="<?= $itm['id'] ?>" <?= $itm['id'] == $item_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($itm['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="invoiceItemReport.php" class="btn btn-outline-secondary">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Invoice Item Report Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Item (Code)</th>
                        <th>Category</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($invoice_items->num_rows > 0): ?>
                        <?php while($item = $invoice_items->fetch_assoc()): ?>
                        <tr>
                            <td><?= $item['invoice_no'] ?></td>
                            <td><?= date('d M Y', strtotime($item['date'])) ?></td>
                            <td><?= $item['title'] . ' ' . $item['first_name'] . ' ' . $item['last_name'] ?></td>
                            <td><?= $item['item_name'] ?> (<?= $item['item_code'] ?>)</td>
                            <td><?= $item['category'] ?></td>
                            <td><?= number_format($item['unit_price'], 2) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($item['amount'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No invoice items found for selected criteria</td>
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
                        <a class="page-link" href="?page=<?= $page-1 ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&customer=<?= $customer_id ?>&item=<?= $item_id ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&customer=<?= $customer_id ?>&item=<?= $item_id ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if($page < $pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page+1 ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&customer=<?= $customer_id ?>&item=<?= $item_id ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>