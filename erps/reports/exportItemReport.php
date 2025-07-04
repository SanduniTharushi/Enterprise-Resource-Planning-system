<?php
include '../config/db.php';

// Set headers for Excel file download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="item_report_'.date('Y-m-d').'.xls"');

// Filters
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$subcategory_id = isset($_GET['subcategory']) ? (int)$_GET['subcategory'] : 0;

// Base query
$query = "SELECT i.item_name, ic.category, isc.sub_category, SUM(i.quantity) as total_quantity
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
            ORDER BY i.item_name";

$items = $conn->query($query);

// Create Excel content
echo "<table border='1'>";
echo "<tr>
        <th>Item Name</th>
        <th>Category</th>
        <th>Sub Category</th>
        <th>Total Quantity</th>
      </tr>";

if ($items->num_rows > 0) {
    while($item = $items->fetch_assoc()) {
        echo "<tr>
                <td>".htmlspecialchars($item['item_name'])."</td>
                <td>".htmlspecialchars($item['category'])."</td>
                <td>".htmlspecialchars($item['sub_category'])."</td>
                <td>".$item['total_quantity']."</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No items found</td></tr>";
}

echo "</table>";
exit;
?>