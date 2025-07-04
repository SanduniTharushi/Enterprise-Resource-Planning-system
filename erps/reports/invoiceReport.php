<?php
include('../includes/header.php');
include '../config/db.php';

// Pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Date range and customer filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$customer_id = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;

// Base query
$query = "SELECT SQL_CALC_FOUND_ROWS i.*, c.title, c.first_name, c.last_name, d.district 
          FROM invoice i
          JOIN customer c ON i.customer = c.id
          JOIN district d ON c.district = d.id
          WHERE i.date BETWEEN '$start_date' AND '$end_date'";

// Add customer filter if selected
if($customer_id > 0) {
    $query .= " AND i.customer = $customer_id";
}

$query .= " ORDER BY i.date DESC, i.time DESC LIMIT $start, $per_page";

$invoices = $conn->query($query);
$total = $conn->query("SELECT FOUND_ROWS() as total")->fetch_assoc()['total'];
$pages = ceil($total / $per_page);

// Get customers for filter dropdown
$customers = $conn->query("SELECT id, CONCAT(title, ' ', first_name, ' ', last_name) as name FROM customer ORDER BY first_name");
?>
<div class="container">

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Invoice Report</h2>
            <div>
                <a href="exportInvoiceReport.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&customer=<?= $customer_id ?>" 
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
                <div class="col-md-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="invoiceReport.php" class="btn btn-outline-secondary">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Invoice Report Table -->
         
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>District</th>
                        <th>Item Count</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($invoices->num_rows > 0): ?>
                        <?php while($invoice = $invoices->fetch_assoc()): ?>
                        <tr>
                            <td><?= $invoice['invoice_no'] ?></td>
                            <td><?= date('d M Y', strtotime($invoice['date'])) ?></td>
                            <td><?= $invoice['title'] . ' ' . $invoice['first_name'] . ' ' . $invoice['last_name'] ?></td>
                            <td><?= $invoice['district'] ?></td>
                            <td><?= $invoice['item_count'] ?></td>
                            <td><?= number_format($invoice['amount'], 2) ?></td>
                            <td>
                                <a href="invoiceDetails.php?id=<?= $invoice['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No invoices found for selected criteria</td>
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
                        <a class="page-link" href="?page=<?= $page-1 ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&customer=<?= $customer_id ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&customer=<?= $customer_id ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if($page < $pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page+1 ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&customer=<?= $customer_id ?>" aria-label="Next">
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