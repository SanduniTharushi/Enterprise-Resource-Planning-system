<?php
include '../config/db.php';
include '../includes/header.php';

$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$searchLike = "%$search%";

// Count total matching customers
$countSql = "SELECT COUNT(*) as total FROM customer c 
             JOIN district d ON c.district = d.id
             WHERE c.first_name LIKE ? OR c.last_name LIKE ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("ss", $searchLike, $searchLike);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRow = $countResult->fetch_assoc();
$total = $totalRow['total'];
$totalPages = ceil($total / $perPage);

// Get paginated customers with district name
$sql = "SELECT c.*, d.district AS district_name FROM customer c 
        JOIN district d ON c.district = d.id 
        WHERE c.first_name LIKE ? OR c.last_name LIKE ? 
        ORDER BY c.id DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $searchLike, $searchLike, $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h2>Customer List</h2>
    <form class="row mb-3" method="GET">
        <div class="col-md-10">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by name">
        </div>
        <div class="col-md-2">
            <button class="btn btn-success">Search</button>
            <a href="listCustomer.php" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <a href="addCustomer.php" class="btn btn-primary mb-3">+ Add New Customer</a>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>District</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td>
                            <?php
                                $nameParts = array_filter([
                                    $row['title'],
                                    $row['first_name'],
                                    $row['middle_name'],
                                    $row['last_name']
                                ]);
                                echo htmlspecialchars(implode(' ', $nameParts));
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row['contact_no']) ?></td>
                        <td><?= htmlspecialchars($row['district_name']) ?></td>
                        <td>
                            <a href="editCustomer.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="deleteCustomer.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page-1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page+1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info">No customers found. <a href="addCustomer.php">Add a new customer</a></div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
