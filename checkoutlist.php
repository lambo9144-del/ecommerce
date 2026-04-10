<?php include('header.php');?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); 

$cartId=isset($_GET['cart_id']) ? (int)$_GET['cart_id'] : 0;

$qry="SELECT *,ci.cart_id receipt_id FROM cart_id ci 
JOIN carts c ON ci.id = c.cart_id
JOIN products p ON c.e_id = p.id
WHERE ci.id = ? ORDER BY c.date_created DESC ";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $cartId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $receiptId = 'N/A';
    $checkoutItems = [];
    $subtotal = 0;
    $serviceFee = 0;
    $grandTotal = 0;
} else {
    $row = $result->fetch_assoc();
    $receiptId = $row['receipt_id'];

    $checkoutItems = [];
    $subtotal = 0;

    foreach ($result as $item) {
        $lineTotal = (float)$item['price'] * (int)$item['quantity'];
        $subtotal += $lineTotal;

        $checkoutItems[] = [
            'id' => (int)$item['p_id'],
            'sku' => htmlspecialchars($item['sku']),
            'price' => (float)$item['price'],
            'qty' => (int)$item['quantity'],
            'line_total' => $lineTotal
        ];
    }

    $serviceFee = round($subtotal * 0.05, 2);
    $grandTotal = round($subtotal + $serviceFee, 2);
}
?>

<div class="product-container checkout-page">
	<div class="header checkout-header">
		<div class="title">Checkout</div>
		<button onclick="window.location.href='cartList.php'" class="btn-add" type="button">Back To Cart</button>
	</div>

	<div class="checkout-card">
		<div class="checkout-section receipt-box">
			<h2>Receipt ID</h2>
			<div class="receipt-id"><?= htmlspecialchars($receiptId) ?></div>
			<p class="receipt-note">Issued on <?= date('d M Y, h:i A') ?></p>
		</div>

		<div class="checkout-section cart-detail-box">
			<h2>Cart Detail</h2>

			<?php if (empty($checkoutItems)) { ?>
				<p class="cart-empty">No items found in your cart.</p>
			<?php } else { ?>
				<div class="cart-table-wrap">
					<table>
						<thead>
							<tr>
								<th>ID</th>
								<th>SKU</th>
								<th>Qty</th>
								<th>Unit Price</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($checkoutItems as $item) { ?>
								<tr>
									<td><?= $item['id'] ?></td>
									<td><?= htmlspecialchars($item['sku']) ?></td>
									<td><?= $item['qty'] ?></td>
									<td class="price">RM <?= number_format($item['price'], 2) ?></td>
									<td class="price">RM <?= number_format($item['line_total'], 2) ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>

				<div class="checkout-summary">
					<div class="summary-row">
						<span>Subtotal</span>
						<strong>RM <?= number_format($subtotal, 2) ?></strong>
					</div>
					<div class="summary-row">
						<span>Service Fee</span>
						<strong>RM <?= number_format($serviceFee, 2) ?></strong>
					</div>
					<div class="summary-row grand">
						<span>Grand Total</span>
						<strong>RM <?= number_format($grandTotal, 2) ?></strong>
					</div>
				</div>
			<?php } ?>
		</div>

		<div class="checkout-section payment-box">
			<h2>Payment Method</h2>
			<form class="payment-method-form" action="#" method="post">
				<label class="payment-option">
					<input type="radio" name="payment_method" checked>
					<span>Credit / Debit Card</span>
				</label>
				<label class="payment-option">
					<input type="radio" name="payment_method">
					<span>Online Banking</span>
				</label>
				<label class="payment-option">
					<input type="radio" name="payment_method">
					<span>E-Wallet</span>
				</label>
                <div class="payment_details" style="display: none;">
					<p>Pay using your preferred E-Wallet app. </p>
					<img src="./ewallet.jpg" alt="E-Wallet" width="400" height="500">>
					<input type="E_wallet" name="payment_proof" accept="image/*">
				</div>
				<button type="button" class="btn-add pay-btn" <?= empty($checkoutItems) ? 'disabled' : '' ?>>Confirm Payment</button>
			</form>
		</div>
	</div>
</div>

<?php include('footer.php'); ?>

<?php include('footer.php');?>