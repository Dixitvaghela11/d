<?php
session_start();
if (!isset($_SESSION['token'])) {
    header('Location: login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $category = $_POST['category'];
    $image = $_FILES['image'];

    $url = 'http://localhost:3000/products';
    $data = [
        'name' => $name,
        'dob' => $dob,
        'category' => $category
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);

    if ($image['size'] > 0) {
        $imageData = new CURLFile($image['tmp_name'], $image['type'], $image['name']);
        $data['image'] = $imageData;
    }

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    curl_close($ch);

    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product CRUD</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <h1>Add Product</h1>
    
    <form action="" method="post" enctype="multipart/form-data">
            <label>Product Name:</label>
            <input type="text" name="name" required>
            <label>DOB:</label>
            <input type="date" name="dob" required>
            <label>Category:</label>
            <select name="category" required>
                <?php
                $categories = json_decode(file_get_contents('http://localhost:3000/categories'), true);
                foreach ($categories as $category) {
                    echo "<option value='{$category['_id']}'>{$category['name']}</option>";
                }
                ?>
            </select>    
            <label>Product Image:</label>
            <input type="file" name="image">
            <input type="submit" value="Add Product">
        </form>
    <h1>Products List</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>DOB</th>
                <th>Product Image</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $products = json_decode(file_get_contents('http://localhost:3000/products'), true);
            foreach ($products as $product) {
                echo "<tr>";
                echo "<td>{$product['name']}</td>";
                echo "<td>{$product['dob']}</td>";
                echo "<td><img src='http://localhost:3000/{$product['image']}' width='100'></td>";
                echo "<td>{$product['category']['name']}</td>";
                echo "<td>
                        <a href='edit_product.php?id={$product['_id']}'>Edit</a> |
                        <a href='delete_product.php?id={$product['_id']}'>Delete</a>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>