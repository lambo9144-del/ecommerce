<?php include('header.php'); 


if(isset($_GET['id'])){
    $id=$_GET['id'];
    $qry=$conn->prepare("SELECT * FROM products WHERE id=?");
    $qry->bind_param("i",$id);
    $qry->execute();
    $result=$qry->get_result();
    $result=$result->fetch_assoc();
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $sku=$_POST["sku"];
    $price=$_POST["price"];
    $quantity=$_POST["quantity"];

    $qry=$conn->prepare("UPDATE products SET sku=?, price=?, quantity=? WHERE id=?");
    $qry->bind_param("sdii",$sku,$price,$quantity,$id);
    if($qry->execute()){
        echo "<script>alert('Product updated successfully');
        window.location.href='product.php';
        </script>";
    } else {
        echo "<script>alert('Failed to update product');
        window.location.href='edit_product.php?id=$id';
        </script>";
    }
}


?>


<div class="container profile-page">
    <div class="profile-card">
        <h1>Edit Product form</h1>
        <form action="" method="post" >
        <div class="form-group">
            <label for="sku">SKU:</label>
            <input type="text" id="sku" name="sku" class="form-control" value="<?=$result['sku']?>">
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="text" id="price" name="price" class="form-control" value="<?=$result['price']?>">
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" class="form-control" value="<?=$result['quantity']?>">
        </div>
        <button type="submit" class="btn btn-primary">Edit Product</button>
        <br>
        <br>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='product.php'">Back to Product List</button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?> 