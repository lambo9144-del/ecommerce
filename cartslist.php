<?php include('header.php'); ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);       
$randomStrings=rand(10000000000,99999999999);


$array=['0','1','2','3','4','5','6','7','8','9',
        'a','b','c','d','e','f','g','h','i','j',
        'k','l','m','n','o','p','q','r','s','t',
        'u','v','w','x','y','z',
        'A','B','C','D','E','F','G','H','I','J',
        'K','L','M','N','O','P','Q','R','S','T',
        'U','V','W','X','Y','Z'];

for($i=0;$i<11;$i++){
    shuffle($array);
    $randomStrings.=implode('', array_slice($array, 0, 10));
}
   

    function cart_redirect(){
        echo "<script>
            alert('Product added to cart');
        window.location.href='cartslist.php';
        </script>";
    }  
    
    

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($action==="checkout"){
    $jsonData=json_decode(file_get_contents('cart.json'), true);
    $sqldata=[];
    $debug = [];
    foreach($jsonData as $p_id => $qty){
        $sqldata[]=[(int)$_SESSION['user_id'], (int)$p_id,(int)$qty];
    }
    $debug[] = 'sqldata: ' . json_encode($sqldata);
    $array=['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f'];
    for($i=0; $i<10; $i++){
        shuffle($array);
        $randomString=implode('', array_slice($array, 0, 10));
    }
    $qry=$conn->prepare("INSERT INTO cart_id(cart_id) VALUES(?)");
    if(!$qry){ $debug[] = 'cart_id prepare failed: ' . $conn->error; }
    $qry->bind_param("s", $randomString);
    if(!$qry->execute()) { $debug[] = 'cart_id execute failed: ' . $qry->error; }
       echo $lastid=$qry->insert_id;
    $debug[] = 'lastid: ' . $lastid;

    $qry=$conn->prepare("INSERT INTO carts(u_id,e_id,cart_id) VALUES (?, ?, ?)");
    if(!$qry){ $debug[] = 'carts prepare failed: ' . $conn->error; }
    foreach($sqldata as $data){
        $qry->bind_param("iiii", $data[0], $data[1], $lastid,$data[2]);
        if(!$qry->execute()) { $debug[] = 'carts execute failed: ' . $qry->error . ' for data: ' . json_encode($data); }
    }
    $_SESSION['cart'] = [];
    file_put_contents('cart.json', json_encode($_SESSION['cart']));
    
    echo "<script>
        alert('Checkout successful');
        window.location.href='checkoutlist.php?cart_id={$lastid}';
    </script>";
}
if ($action === 'add' && $id > 0) {
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += 1;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
    cart_redirect();
}

if ($action === 'remove' && $id > 0) {
    unset($_SESSION['cart'][$id]);
    cart_redirect();
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    cart_redirect();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart']) && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $productId => $qty) {
        $productId = (int)$productId;
        $qty = (int)$qty;

        if ($productId <= 0) {
            continue;
        }

        if ($qty <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = $qty;
        }
    }
    cart_redirect();
}

$cartItems = [];
$grandTotal = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId => $cartQty) {
        $productId = (int)$productId;
        $cartQty = (int)$cartQty;

        $qry = $conn->prepare("SELECT id, sku, price, quantity FROM products WHERE id = ? LIMIT 1");
        $qry->bind_param("i", $productId);
        $qry->execute();
        $result = $qry->get_result();

        if ($result->num_rows === 0) {
            unset($_SESSION['cart'][$productId]);
            continue;
        }

        $row = $result->fetch_assoc();
        $price = (float)$row['price'];
        $stock = (int)$row['quantity'];
        $safeQty = min(max($cartQty, 1), max($stock, 1));
        $_SESSION['cart'][$productId] = $safeQty;

        $subtotal = $price * $safeQty;
        $grandTotal += $subtotal;

        $cartItems[] = [
            'id' => (int)$row['id'],
            'sku' => $row['sku'],
            'price' => $price,
            'stock' => $stock,
            'qty' => $safeQty,
            'subtotal' => $subtotal
        ];
    }
}
?>

<div class="product-container">
    <div class="header">
        <div class="title">Cart List</div>
        <button onclick="window.location.href='product.php'" class="btn-add">Continue Shopping</button>
    </div>

    <div class="cart-card">
        <?php if (empty($cartItems)) { ?>
            <p class="cart-empty">Your cart is empty.</p>
        <?php } else { ?>
            <form method="post" action="cartList.php">
                <div class="cart-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item) { ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['sku']) ?></td>
                                    <td class="price">RM <?= number_format($item['price'], 2) ?></td>
                                    <td><?= $item['stock'] ?></td>
                                    <td>
                                        <input
                                            type="number"
                                            name="qty[<?= $item['id'] ?>]"
                                            min="0"
                                            max="<?= max($item['stock'], 1) ?>"
                                            value="<?= $item['qty'] ?>"
                                            class="cart-qty-input"
                                        >
                                    </td>
                                    <td class="price">$<?= number_format($item['subtotal'], 2) ?></td>
                                    <td>
                                        <a class="cart-remove" href="cartList.php?action=remove&id=<?= $item['id'] ?>">Remove</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <strong class="cart-total">Total: $<?= number_format($grandTotal, 2) ?></strong>
                </div>

                <div class="cart-actions">
                    <button type="submit" name="update_cart" class="btn-add">Update Cart</button>
                    <button type="button" class="btn-add" onclick="window.location.href='cartslist.php?action=clear'">Clear Cart</button>
                    <button type="button" class="btn-primary" onclick="window.location.href='cartslist.php?action=checkout'">Checkout</button>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<?php include('footer.php'); ?>

