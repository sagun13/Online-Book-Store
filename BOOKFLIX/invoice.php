<?php
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Initialize Dompdf
$dompdf = new Dompdf;

// Include database configuration
include 'config.php';

// Fetch order details from the database
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $get_order = mysqli_query($conn, "SELECT * FROM `confirm_order` WHERE order_id = '$order_id'") or die('query failed');
    if (mysqli_num_rows($get_order) > 0) {
        $fetch_order = mysqli_fetch_assoc($get_order);
    }
    $get_order = mysqli_query($conn, "SELECT * FROM `orders` WHERE id = '$order_id'") or die('query failed');
    if (mysqli_num_rows($get_order) > 0) {
        $fetch_details = mysqli_fetch_assoc($get_order);
    }
}

// HTML content for the invoice
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            color: brown;
            font-weight: bold;
            font-size: 30px;
        }
        .invoice-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .details {
            margin-bottom: 20px;
        }
        .details h3 {
            margin-top: 0;
            font-size: 18px;
        }
        .details p {
            margin: 5px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .sign {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
        .sold-by {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            
            <div class="invoice-title">Invoice Details</div>
        </div>
        <div class="details">
            <div class="details">
                <h3>SHIPPING ADDRESS:</h3>
                <p>To,   ' . $fetch_order['name'] . '</p>
                <p>' . $fetch_details['address'] . '</p>
                <p>' . $fetch_details['city'] . '</p>
                <p>' . $fetch_details['state'] . '</p>
                <p>' . $fetch_details['country'] . '</p>
                <p>' . $fetch_details['pincode'] . '</p>
            </div>
           
            <div class="details">
                <h3>Order Details:</h3>
                <p>Invoice Date:  ' . $fetch_order['date'] . '</p>
                <p>Order ID: ' . $fetch_order['order_id'] . '</p>
                <p>Order Date: ' . $fetch_order['order_date'] . '</p>
                
                <p>Payment Method: ' . $fetch_order['payment_method'] . '</p>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Book Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';

// Fetch and display book details
$select_book = mysqli_query($conn, "SELECT * FROM `orders` WHERE id = '$order_id'") or die('query failed');
$s = 1;
$total_price = 0;
while ($fetch_book = mysqli_fetch_assoc($select_book)) {
    $total_price += $fetch_book['sub_total'];
    $html .= '
    <tr>
        <td>' . $s . '</td>
        <td>' . $fetch_book['book'] . '</td>
        <td>' . $fetch_book['quantity'] . '</td>
        <td>' . $fetch_book['unit_price'] . '</td>
        <td>' . $fetch_book['sub_total'] . '</td>
    </tr>';
    $s++;
}

$html .= '
                <tr>
                    <td colspan="4">NET TOTAL</td>
                    <td>' . $total_price . '</td>
                </tr>

                
            </tbody>
        </table>
        
        <div class="sold-by">
            <h3>SOLD BY:</h3>
            <p>S P Book Store</p>
            <p>Pokhara, Nepal</p>
            <p>Gandaki</p>
        </div>
    </div>
</body>
</html>';

// Load HTML content into Dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF to browser
$dompdf->stream('invoice.pdf');
?>
