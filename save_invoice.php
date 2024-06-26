<?php
// Step 1: Establish a Database Connection
$conn = new mysqli('127.0.0.1:3306', 'root', '', 'dbms');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Generate Invoice Number
// Retrieve the last invoice number from the database and increment it
$sql = "SELECT MAX(invoice_no) AS max_invoice FROM invoice";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$lastInvoiceNumber = $row['max_invoice'];
$newInvoiceNumber = ++$lastInvoiceNumber; // Increment the last invoice number

// Step 3: Save Invoice Details into Database
$productCode = $_POST['productCode'];
$quantity = $_POST['quantity'];

// Fetch product details from database based on product code
$sql = "SELECT product_name, mrp, rate FROM product WHERE product_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productCode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch product details
    $row = $result->fetch_assoc();
    $productName = $row['product_name'];
    $mrp = $row['mrp'];
    $rate = $row['rate'];

    // Calculate total amount
    $total = $rate * $quantity;

    // Insert invoice details into database
    $sql = "INSERT INTO invoice (invoice_no, invoice_date, total_amount) 
            VALUES (?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sd", $newInvoiceNumber, $total);
    $stmt->execute();

    echo "Invoice saved successfully. Invoice Number: " . $newInvoiceNumber;
} else {
    echo "Invalid product code!";
}

// Step 4: Close the Connection
$stmt->close();
$conn->close();
?>
