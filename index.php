<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'smallshop';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Task 0: Create Category table
$createCategoryTable = "CREATE TABLE Category (
    id INT NOT NULL PRIMARY KEY,
    name VARCHAR(255)
)";
if ($conn->query($createCategoryTable) !== true) {
    die("Error creating Category table: " . $conn->error);
}

// Task 1: Create Product table
$createProductTable = "CREATE TABLE Product (
    id INT NOT NULL PRIMARY KEY,
    name VARCHAR(255),
    categoryId INT,
    price DECIMAL(10, 2),  -- Added price column
    FOREIGN KEY (categoryId) REFERENCES Category(id)
)";
if ($conn->query($createProductTable) !== true) {
    die("Error creating Product table: " . $conn->error);
}

// Task 2: Create Cart table
$createCartTable = "CREATE TABLE Cart (
    userId INT NOT NULL,
    productId INT NOT NULL,
    FOREIGN KEY (productId) REFERENCES Product(id)
)";
if ($conn->query($createCartTable) !== true) {
    die("Error creating Cart table: " . $conn->error);
}

// Task 3: Create Users table
$createUsersTable = "CREATE TABLE Users (
    id INT NOT NULL PRIMARY KEY,
    name VARCHAR(255)
)";
if ($conn->query($createUsersTable) !== true) {
    die("Error creating Users table: " . $conn->error);
}

// Task 4: Insert data into Category table
$insertCategoryData = "INSERT INTO Category (id, name) VALUES (1, 'Category 1'), (2, 'Category 2')";
if ($conn->query($insertCategoryData) !== true) {
    die("Error inserting data into Category table: " . $conn->error);
}

// Task 5: Insert data into Product table
$insertProductData = "INSERT INTO Product (id, name, categoryId, price) VALUES ";
for ($i = 1; $i <= 10; $i++) {
    $name = "Product " . $i;
    $categoryId = ($i % 2) + 1; // Assign a category ID based on the pattern
    $price = rand(10, 100);  // Assign a random price between 10 and 100
    $insertProductData .= "($i, '$name', $categoryId, $price), ";
}
$insertProductData = rtrim($insertProductData, ", ");
if ($conn->query($insertProductData) !== true) {
    die("Error inserting data into Product table: " . $conn->error);
}

// Task 6: Insert data into Users table
$insertUserData = "INSERT INTO Users (id, name) VALUES ";
for ($i = 1; $i <= 10; $i++) {
    $name = "User " . $i;
    $insertUserData .= "($i, '$name'), ";
}
$insertUserData = rtrim($insertUserData, ", ");
if ($conn->query($insertUserData) !== true) {
    die("Error inserting data into Users table: " . $conn->error);
}

// Task 7: Insert data into Cart table
$insertCartData = "INSERT INTO Cart (userId, productId) VALUES ";
for ($i = 1; $i <= 10; $i++) {
    $userId = $i;
    $productId = $i;
    $insertCartData .= "($userId, $productId), ";
}
$insertCartData = rtrim($insertCartData, ", ");
if ($conn->query($insertCartData) !== true) {
    die("Error inserting data into Cart table: " . $conn->error);
}

// Task 8: Retrieve data from Cart table
$queryCartRecords = "SELECT Users.name AS userName, Category.name AS categoryName, Product.name AS productName, Product.price  -- Added price column
                     FROM Cart
                     INNER JOIN Users ON Cart.userId = Users.id
                     INNER JOIN Product ON Cart.productId = Product.id
                     INNER JOIN Category ON Product.categoryId = Category.id";
$resultCartRecords = $conn->query($queryCartRecords);
if ($resultCartRecords === false) {
    die("Error retrieving data from Cart table: " . $conn->error);
}
$cartRecords = $resultCartRecords->fetch_all(MYSQLI_ASSOC);

// Display the results
echo "Cart Records (User, Category, Product, Price):\n";
print_r($cartRecords);

// Task 1: Retrieve the number of users
$queryNumUsers = "SELECT COUNT(*) AS count FROM users";
$resultNumUsers = $conn->query($queryNumUsers);

if ($resultNumUsers === false) {
    echo "Error executing query: " . $conn->error;
} else {
    $row = $resultNumUsers->fetch_assoc();
    $numUsers = $row['count'];
    echo "Task 1: Number of users: $numUsers<br>";
}

// Task 2: Retrieve the number of products in the cart for user with ID 1
$queryNumProductsInCart = "SELECT COUNT(*) AS count FROM cart WHERE userId = 1";
$resultNumProductsInCart = $conn->query($queryNumProductsInCart);

if ($resultNumProductsInCart === false) {
    echo "Error executing query: " . $conn->error;
} else {
    $row = $resultNumProductsInCart->fetch_assoc();
    $numProductsInCart = $row['count'];
    echo "Task 2: Number of products in the cart for user with ID 1: $numProductsInCart<br>";
}

// Task 3: Retrieve the total value of products in the cart
$queryTotalCartValue = "SELECT SUM(product.price * cart.quantity) AS total
                        FROM cart
                        INNER JOIN product ON cart.productId = product.id";
$resultTotalCartValue = $conn->query($queryTotalCartValue);

if ($resultTotalCartValue === false) {
    echo "Error executing query: " . $conn->error;
} else {
    $row = $resultTotalCartValue->fetch_assoc();
    $totalCartValue = $row['total'];
    echo "Task 3: Total value of products in the cart: $totalCartValue<br>";
}

// Task 4: Retrieve the average price of products in the cart
$queryAverageCartPrice = "SELECT AVG(product.price) AS average
                          FROM cart
                          INNER JOIN product ON cart.productId = product.id";
$resultAverageCartPrice = $conn->query($queryAverageCartPrice);

if ($resultAverageCartPrice === false) {
    echo "Error executing query: " . $conn->error;
} else {
    $row = $resultAverageCartPrice->fetch_assoc();
    $averageCartPrice = $row['average'];
    echo "Task 4: Average price of products in the cart: $averageCartPrice<br>";
}

// Task 5: Retrieve the number of each product in the cart for user with ID 1
$queryNumEachProductInCart = "SELECT product.id AS productId, product.name, COUNT(*) AS count
                              FROM cart
                              INNER JOIN product ON cart.productId = product.id
                              WHERE cart.userId = 1
                              GROUP BY product.id, product.name";
$resultNumEachProductInCart = $conn->query($queryNumEachProductInCart);

if ($resultNumEachProductInCart === false) {
    echo "Error executing query: " . $conn->error;
} else {
    echo "Task 5: Number of each product in the cart for user with ID 1:<br>";
    while ($row = $resultNumEachProductInCart->fetch_assoc()) {
        $productId = $row['productId'];
        $productName = $row['name'];
        $count = $row['count'];
        echo "Product ID: $productId, Product Name: $productName, Count: $count<br>";
    }
}

// Task 6: Retrieve the number of products in the cart for each user
$queryNumProductsInCartPerUser = "SELECT users.id AS userId, users.name, COUNT(*) AS count
                                  FROM cart
                                  INNER JOIN users ON cart.userId = users.id
                                  GROUP BY users.id, users.name";
$resultNumProductsInCartPerUser = $conn->query($queryNumProductsInCartPerUser);

if ($resultNumProductsInCartPerUser === false) {
    echo "Error executing query: " . $conn->error;
} else {
    echo "Task 6: Number of products in the cart for each user:<br>";
    while ($row = $resultNumProductsInCartPerUser->fetch_assoc()) {
        $userId = $row['userId'];
        $userName = $row['name'];
        $count = $row['count'];
        echo "User ID: $userId, User Name: $userName, Count: $count<br>";
    }
}

// Task 7: Retrieve the top 3 most frequently added products in the cart
$queryTop3ProductsInCart = "SELECT product.id AS productId, product.name, COUNT(*) AS count
                            FROM cart
                            INNER JOIN product ON cart.productId = product.id
                            GROUP BY product.id, product.name
                            ORDER BY COUNT(*) DESC
                            LIMIT 3";
$resultTop3ProductsInCart = $conn->query($queryTop3ProductsInCart);

if ($resultTop3ProductsInCart === false) {
    echo "Error executing query: " . $conn->error;
} else {
    echo "Task 7: Top 3 most frequently added products in the cart:<br>";
    while ($row = $resultTop3ProductsInCart->fetch_assoc()) {
        $productId = $row['productId'];
        $productName = $row['name'];
        $count = $row['count'];
        echo "Product ID: $productId, Product Name: $productName, Count: $count<br>";
    }
}

// Task 8: Retrieve users who haven't purchased anything
$queryUsersWithoutPurchase = "SELECT users.id AS userId, users.name
                              FROM users
                              WHERE users.id NOT IN (SELECT DISTINCT userId FROM cart)";
$resultUsersWithoutPurchase = $conn->query($queryUsersWithoutPurchase);

if ($resultUsersWithoutPurchase === false) {
    echo "Error executing query: " . $conn->error;
} else {
    echo "Task 8: Users who haven't purchased anything:<br>";
    while ($row = $resultUsersWithoutPurchase->fetch_assoc()) {
        $userId = $row['userId'];
        $userName = $row['name'];
        echo "User ID: $userId, User Name: $userName<br>";
    }
}

// Task 9: Retrieve the user who purchased the highest quantity of a single product
$queryMaxProductQuantity = "SELECT cart.userId, users.name, cart.productId, product.name AS productName, MAX(cart.quantity) AS maxQuantity
                            FROM cart
                            INNER JOIN users ON cart.userId = users.id
                            INNER JOIN product ON cart.productId = product.id
                            GROUP BY cart.userId, users.name, cart.productId, product.name
                            ORDER BY MAX(cart.quantity) DESC
                            LIMIT 1";
$resultMaxProductQuantity = $conn->query($queryMaxProductQuantity);

if ($resultMaxProductQuantity === false) {
    echo "Error executing query: " . $conn->error;
} else {
    echo "Task 9: User who purchased the highest quantity of a single product:<br>";
    while ($row = $resultMaxProductQuantity->fetch_assoc()) {
        $userId = $row['userId'];
        $userName = $row['name'];
        $productId = $row['productId'];
        $productName = $row['productName'];
        $maxQuantity = $row['maxQuantity'];
        echo "User ID: $userId, User Name: $userName, Product ID: $productId, Product Name: $productName, Max Quantity: $maxQuantity<br>";
    }
}

// Task 10: Retrieve the cheapest product, users who purchased it, and the total price of all purchased products
$queryCheapestProduct = "SELECT product.id AS productId, product.name, MIN(product.price) AS minPrice, COUNT(*) AS count, SUM(product.price) AS totalPrice
                         FROM cart
                         INNER JOIN product ON cart.productId = product.id
                         GROUP BY product.id, product.name
                         ORDER BY MIN(product.price) ASC
                         LIMIT 1";
$resultCheapestProduct = $conn->query($queryCheapestProduct);

if ($resultCheapestProduct === false) {
    echo "Error executing query: " . $conn->error;
} else {
    echo "Task 10: Cheapest product, users who purchased it, and total price of all purchased products:<br>";
    while ($row = $resultCheapestProduct->fetch_assoc()) {
        $productId = $row['productId'];
        $productName = $row['name'];
        $minPrice = $row['minPrice'];
        $count = $row['count'];
        $totalPrice = $row['totalPrice'];
        echo "Product ID: $productId, Product Name: $productName, Min Price: $minPrice, Count: $count, Total Price: $totalPrice<br>";
    }
}

$conn->close();
?>