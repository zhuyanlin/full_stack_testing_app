<?php
// Database connection settings
$host = 'mysql_container';  // MySQL container name in Docker
$db   = 'cart_db';          // Database name
$user = 'full_stack_tester'; // MySQL user from .env
$pass = 'zhuyanlin0524';     // MySQL password from .env
$charset = 'utf8mb4';

// DSN (Data Source Name) for PDO connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Enable exceptions for error handling
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Establish a database connection
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Return error message in JSON format
    header('Content-Type: application/json');
    echo json_encode(["status" => 500, "error" => "Connection failed: " . $e->getMessage()]);
    exit;
}

// Handle REST API requests
$method = $_SERVER['REQUEST_METHOD'];

// Set header to return JSON data
header('Content-Type: application/json');

// 1. Handle POST request to add a cart item (Create a cart)
if ($method == 'POST') {
    // Validate input
    if (isset($_POST['title'], $_POST['size'], $_POST['price'], $_POST['quantity'], $_POST['image'])) {
        $title = $_POST['title'];
        $size = $_POST['size'];
        $price = $_POST['price'];
        $image = $_POST['image'];
        $quantity = $_POST['quantity'];

        try {
            // Check if the item with the same title and size already exists in the cart
            $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE title = ? AND size = ?");
            $stmt->execute([$title, $size]);
            $existingItem = $stmt->fetch();

            if ($existingItem) {
                // If the item exists, update the quantity
                $stmt = $pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE title = ? AND size = ?");
                $stmt->execute([$quantity, $title, $size]);
                echo json_encode(["status" => 200, "message" => "Item updated in cart"]);
            } else {
                // Otherwise, insert the new item into the cart
                $stmt = $pdo->prepare("INSERT INTO cart_items (title, size, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $size, $price, $quantity, $image]);
                echo json_encode(["status" => 201, "message" => "Item added to cart"]);
            }
        } catch (Exception $e) {
            // Handle any errors and return a JSON response
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => 400, "error" => "Invalid input"]);
    }
}

// 2. Handle GET request to retrieve all cart items (Get a cart)
if ($method == 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM cart_items");
        $items = $stmt->fetchAll();
        echo json_encode(["status" => 200, "data" => $items]);
    } catch (Exception $e) {
        echo json_encode(["status" => 500, "error" => $e->getMessage()]);
    }
}

// 3. Handle PUT request to update an item in the cart (Update a cart)
if ($method == 'PUT') {
    // Parse incoming PUT data
    parse_str(file_get_contents("php://input"), $_PUT);

    if (isset($_PUT['title'], $_PUT['size'], $_PUT['quantity'])) {
        $title = $_PUT['title'];
        $size = $_PUT['size'];
        $quantity = $_PUT['quantity'];

        try {
            // Update the quantity of the specified item in the cart
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE title = ? AND size = ?");
            $stmt->execute([$quantity, $title, $size]);
            echo json_encode(["status" => 200, "message" => "Item updated in cart"]);
        } catch (Exception $e) {
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => 400, "error" => "Invalid input"]);
    }
}

// 4. Handle DELETE request to remove an item from the cart
if ($method == 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);

    if (isset($_DELETE['title'], $_DELETE['size'])) {
        $title = $_DELETE['title'];
        $size = $_DELETE['size'];

        try {
            // Delete the specified item from the cart
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE title = ? AND size = ?");
            $stmt->execute([$title, $size]);
            echo json_encode(["status" => 200, "message" => "Item removed from cart"]);
        } catch (Exception $e) {
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => 400, "error" => "Invalid input"]);
    }
}
