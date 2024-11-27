<?php
$id = $_GET['id'];
$product = json_decode(file_get_contents("http://localhost:3000/products/$id"), true);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $category = $_POST['category'];
    $image = $_FILES['image'];

    $url = "http://localhost:3000/products/$id";
    $data = [
        'name' => $name,
        'dob' => $dob,
        'category' => $category
    ];

    if ($image['size'] > 0) {
        $imageData = new CURLFile($image['tmp_name'], $image['type'], $image['name']);
        $data['image'] = $imageData;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);

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
    <title>Edit Product</title>
</head>
<body>
    <h1>Edit Product</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $product['_id']; ?>">
        Product Name: <input type="text" name="name" value="<?php echo $product['name']; ?>" required><br>
        DOB: <input type="date" name="dob" value="<?php echo $product['dob']; ?>" required><br>
        Product Image: <input type="file" name="image"><br>
        Category: 
        <select name="category" required>
            <?php
            $categories = json_decode(file_get_contents('http://localhost:3000/categories'), true);
            foreach ($categories as $category) {
                $selected = $category['_id'] === $product['category']['_id'] ? 'selected' : '';
                echo "<option value='{$category['_id']}' $selected>{$category['name']}</option>";
            }
            ?>
        </select><br>
        <input type="submit" value="Update Product">
    </form>
</body>
</html>