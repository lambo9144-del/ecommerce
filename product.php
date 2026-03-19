<?php 
include('header.php'); 

$qry=$conn->prepare("SELECT * FROM products");
$qry->execute();
$result=$qry->get_result();

?>

<div class="product-container">
    <div class="header">
        <div class="title">Product List</div>
        <button onclick="window.location.href='add_product.php'" class="btn-add">+ Add Product</button>
    </div>

    <div class="card">
        <div class="search-box">
            <input type="text" placeholder="Search product...">
        </div>
    </div>

    <table>
        <thead>
            <tr>
            <th>ID</th>
            <th>SKU</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Date Created</th>
            <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row=$result->fetch_assoc()){ ?>
                <tr>
                    <td><?=$row['id']?></td>
                    <td><?=$row['sku']?></td>
                    <td><?=$row['price']?></td>
                    <td><?=$row['quantity']?></td>
                    <td><?=$row['date_created']?></td>
                    <td><?=$row['date_updated']?></td>
                    <td>
                        <a href="edit_product.php?id=<?=$row['id']?>" class="btn btn-success">Edit</a>
                        <a href="#" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
</div>

<?php include('footer.php'); ?>