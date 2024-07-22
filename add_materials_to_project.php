<?php
session_start();

include("connection.php");
include("functions.php");

$user_data = check_login($con);

$project_id = $_POST['project_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve selected materials data from the form
    $selectedMaterials = isset($_POST['materials']) ? $_POST['materials'] : [];

    // Iterate over selected materials
    foreach ($selectedMaterials as $materialId => $material) {
        $materialName = $material['name'];
        $materialQuantity = $material['quantity'];

        // Retrieve the price and reorder point of the product
        $productQuery = "SELECT price, quantity, reorder_point FROM products WHERE name = '$materialName'";
        $productResult = mysqli_query($con, $productQuery);

        if (!$productResult) {
            echo "Error retrieving product details: " . mysqli_error($con);
            continue;
        }

        if (mysqli_num_rows($productResult) > 0) {
            $productData = mysqli_fetch_assoc($productResult);
            $productPrice = $productData['price'];
            $productQuantity = $productData['quantity'];
            $reorderPoint = $productData['reorder_point'];

            // Check if the quantity to subtract is valid
            if ($materialQuantity <= $productQuantity) {
                // Perform the subtraction
                $updatedProductQuantity = (int)$productQuantity - (int)$materialQuantity;

                // Update the products table with the new quantity
                $updateProductQuery = "UPDATE products SET quantity = $updatedProductQuantity WHERE name = '$materialName'";
                $updateProductResult = mysqli_query($con, $updateProductQuery);

                if (!$updateProductResult) {
                    echo "Error updating product quantity: " . mysqli_error($con);
                    continue;
                }

                // Check if the material already exists for the project
                $existingMaterialQuery = "SELECT * FROM `material` WHERE `project_id` = $project_id AND `name` = '$materialName'";
                $existingMaterialResult = mysqli_query($con, $existingMaterialQuery);

                if (!$existingMaterialResult) {
                    echo "Error checking existing material: " . mysqli_error($con);
                    continue;
                }

                if (mysqli_num_rows($existingMaterialResult) > 0) {
                    // Material already exists, update its quantity and price
                    $existingMaterialData = mysqli_fetch_assoc($existingMaterialResult);
                    $existingMaterialId = $existingMaterialData['material_id'];
                    $existingMaterialQuantity = $existingMaterialData['quantity'];

                    $updatedQuantity = (int)$existingMaterialQuantity + (int)$materialQuantity;

                    // Update the quantity and price of the existing material
                    $updateQuery = "UPDATE `material` SET `quantity` = $updatedQuantity, `price` = $productPrice WHERE `material_id` = $existingMaterialId";

                    // Wrap the database operation in a try-catch block
                    try {
                        $updateResult = mysqli_query($con, $updateQuery);
                        if (!$updateResult) {
                            throw new Exception(mysqli_error($con));
                        }

                        // Calculate the new total price
                        $materialQuantity = (int)$updatedQuantity;
                        $productPrice = (float)$productPrice;
                        $totalPrice = $materialQuantity * $productPrice;

                        // Update the total_price column in the material table
                        $updateTotalPriceQuery = "UPDATE `material` SET `total_price` = $totalPrice WHERE `material_id` = $existingMaterialId";
                        $updateTotalPriceResult = mysqli_query($con, $updateTotalPriceQuery);

                        if (!$updateTotalPriceResult) {
                            throw new Exception(mysqli_error($con));
                        }

                        echo "Materials Added!";
                    } catch (Exception $e) {
                        echo "Error updating material: " . $e->getMessage();
                        continue;
                    }
                } else {
                    if ($materialQuantity > 0) { // Only insert if quantity is greater than 0
                        // Material does not exist, insert it as a new entry with price
                        $insertQuery = "INSERT INTO `material` (project_id, name, quantity, price) VALUES ($project_id, '$materialName', $materialQuantity, $productPrice)";

                        // Wrap the database operation in a try-catch
                        try {
                            $insertResult = mysqli_query($con, $insertQuery);
                            if (!$insertResult) {
                                throw new Exception(mysqli_error($con));
                            }

                            // Retrieve the newly inserted material's ID
                            $newMaterialId = mysqli_insert_id($con);

                            // Calculate the total price for the new material
                            $materialQuantity = (int)$materialQuantity;
                            $productPrice = (float)$productPrice;
                            $totalPrice = $materialQuantity * $productPrice;

                            // Update the total_price column in the material table for the new material
                            $updateTotalPriceQuery = "UPDATE `material` SET `total_price` = $totalPrice WHERE `material_id` = $newMaterialId";
                            $updateTotalPriceResult = mysqli_query($con, $updateTotalPriceQuery);

                            if (!$updateTotalPriceResult) {
                                throw new Exception(mysqli_error($con));
                            }

                            echo "Materials Added!";
                        } catch (Exception $e) {
                            echo "Error inserting material: " . $e->getMessage();
                            continue;
                        }
                    }
                }

                // Check if the product needs to be reordered
                if ($updatedProductQuantity <= $reorderPoint) {
                    // Update the product status to "reorder"
                    $updateStatusQuery = "UPDATE products SET status = 'reorder' WHERE name = '$materialName'";
                    $updateStatusResult = mysqli_query($con, $updateStatusQuery);

                    if (!$updateStatusResult) {
                        echo "Error updating product status: " . mysqli_error($con);
                        continue;
                    }
                     // Insert into the audit trail
                $login_user_id = $_SESSION['user_id'];
                $action = "Added materials";
                $timestamp = date('Y-m-d H:i:s');
                $details = "(Project ID: $project_id, Material Name: $materialName, Quantity: $materialQuantity)";
                $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
                $audit_result = mysqli_query($con, $audit_query);

                if (!$audit_result) {
                    echo "Error inserting into audit trail: " . mysqli_error($con);
                }
                }

            } else {
                echo "Invalid quantity to subtract for material: " . $materialName;
                continue;
            }

            // Calculate the sum of the total price for all materials used in the project
            $materialTotalPriceQuery = "SELECT SUM(total_price) AS total_price FROM `material` WHERE `project_id` = $project_id";
            $materialTotalPriceResult = mysqli_query($con, $materialTotalPriceQuery);

            if (!$materialTotalPriceResult) {
                echo "Error calculating total price of materials: " . mysqli_error($con);
            } else {
                $materialTotalPriceData = mysqli_fetch_assoc($materialTotalPriceResult);
                $materialTotalPrice = $materialTotalPriceData['total_price'];

                // Update the actual_cost column in the project table with the sum of the total price
                $updateActualCostQuery = "UPDATE `project` SET `actual_cost` = $materialTotalPrice WHERE `project_id` = $project_id";
                $updateActualCostResult = mysqli_query($con, $updateActualCostQuery);

                if (!$updateActualCostResult) {
                    echo "Error updating actual cost of project: " . mysqli_error($con);
                } else {
                    echo "Materials added and actual cost of project updated!";
                }
            }
        }
    }
}
include('layouts/header.php');
?>


<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
    }

    h1 {
        font-size: 24px;
        margin-bottom: 20px;
    }

    .search-container {
        margin-bottom: 20px;
    }

    .search-container input[type="text"] {
        padding: 10px;
        font-size: 16px;
        width: 50%;
    }

    .product-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .product-table th,
    .product-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ccc;
    }

    .product-table th {
        background-color: #f5f5f5;
    }

    .product-table input[type="number"] {
        padding: 8px;
        width: 60px;
    }

    .product-table button {
        padding: 8px 12px;
        background-color: #4CAF50;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    #search-results {
        margin-bottom: 20px;
    }
</style>

<div class="main-content">
    <h1>Add Materials to Project</h1>

    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search for products">
    </div>

    <div id="search-results">
        <!-- Display search results here -->
    </div>

    <form id="add-materials-form" action="add_materials_to_project.php" method="POST">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

        <table class="product-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Quantity to Add</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="product-table-body">
                <!-- Display products here -->
            </tbody>
        </table>
    </form>

    <script>
        // Function to update the product table based on search results
        function updateProductTable(products) {
            let tableBody = document.getElementById('product-table-body');
            tableBody.innerHTML = '';

            for (let i = 0; i < products.length; i++) {
                let product = products[i];
                let row = document.createElement('tr');
                let nameCell = document.createElement('td');
                let quantityCell = document.createElement('td');
                let quantityToAddCell = document.createElement('td');
                let actionCell = document.createElement('td');
                let quantityInput = document.createElement('input');
                let nameInput = document.createElement('input');
                let addButton = document.createElement('button');

                nameInput.type = 'hidden';
                nameInput.name = `materials[${product.id}][name]`;
                nameInput.value = product.name;

                quantityInput.type = 'number';
                quantityInput.name = `materials[${product.id}][quantity]`;
                quantityInput.min = '0';
                quantityInput.max = product.quantity;

                addButton.type = 'button';
                addButton.textContent = 'Add';
                addButton.addEventListener('click', function () {
                    // Get the quantity to add
                    let quantityToAdd = parseInt(quantityInput.value);

                    if (quantityToAdd > 0) {
                        // Update the form quantity input
                        quantityInput.value = quantityToAdd;

                        // Submit the form
                        document.forms['add-materials-form'].submit();
                    }
                });

                nameCell.textContent = product.name;
                quantityCell.textContent = product.quantity;

                quantityToAddCell.appendChild(quantityInput);
                actionCell.appendChild(addButton);

                row.appendChild(nameCell);
                row.appendChild(quantityCell);
                row.appendChild(quantityToAddCell);
                row.appendChild(actionCell);
                row.appendChild(nameInput);

                tableBody.appendChild(row);
            }
        }

      // Function to perform a search for products based on the search input value
function searchProducts() {
  let searchInput = document.getElementById('search-input');
  let searchValue = searchInput.value.trim().toLowerCase();

  if (searchValue.length > 0) {
    fetch(`search_products_proj.php?search=${searchValue}`)
      .then(response => response.json())
      .then(data => {
        updateProductTable(data.products);
      })
      .catch(error => {
        console.error('Error:', error);
      });
  } else {
    // Clear the product table if the search input is empty
    updateProductTable([]);
  }
}

// Event listener for the search input
let searchInput = document.getElementById('search-input');
searchInput.addEventListener('input', searchProducts);

function addMaterial() {
  var materialSelect = document.getElementById("material");
  var materialQuantity = document.getElementById("quantity").value;

  if (materialSelect.selectedIndex !== 0 && materialQuantity.trim() !== "") {
    var materialId = materialSelect.options[materialSelect.selectedIndex].value;
    var materialName = materialSelect.options[materialSelect.selectedIndex].text;

    // Make an AJAX request to the server
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_material.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    // Prepare the data to be sent
    var data = "material_id=" + encodeURIComponent(materialId) + "&quantity=" + encodeURIComponent(materialQuantity);
    
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        var response = xhr.responseText;
        
        // Display the server response
        alert(response);
        
        // Reset the form inputs
        document.getElementById("quantity").value = "";
        materialSelect.selectedIndex = 0;
      }
    };
    
    // Send the AJAX request with the data
    xhr.send(data);
  } else {
    alert("Please select a material and enter a valid quantity!");
  }
}


// Attach click event handlers to the Add buttons
let addButtons = document.querySelectorAll('.product-table button');
addButtons.forEach(button => {
  button.addEventListener('click', addMaterials);
});

    </script>

</div>
    