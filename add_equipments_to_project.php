<?php
session_start();

include("connection.php");
include("functions.php");

$user_data = check_login($con);

$project_id = $_POST['project_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve selected equipment data from the form
    $selectedEquipment = isset($_POST['equipment']) ? $_POST['equipment'] : [];

    // Iterate over selected equipment
    foreach ($selectedEquipment as $equipmentId => $equipment) {
        $equipmentName = $equipment['name'];
        $equipmentQuantity = $equipment['quantity'];

        // Check if the equipment already exists for the project
        $existingEquipmentQuery = "SELECT * FROM `project_equipment` WHERE `project_id` = $project_id AND `equipment_name` = '$equipmentName'";
        $existingEquipmentResult = mysqli_query($con, $existingEquipmentQuery);

        if (!$existingEquipmentResult) {
            echo "Error checking existing equipment: " . mysqli_error($con);
            continue;
        }

        if (mysqli_num_rows($existingEquipmentResult) > 0) {
            // Equipment already exists, update its quantity
            $existingEquipmentData = mysqli_fetch_assoc($existingEquipmentResult);
            $existingEquipmentId = $existingEquipmentData['project_equipment_id'];
            $existingEquipmentQuantity = $existingEquipmentData['quantity'];

            $updatedQuantity = (int)$existingEquipmentQuantity + (int)$equipmentQuantity;

            // Update the quantity of the existing equipment
            $updateQuery = "UPDATE `project_equipment` SET `quantity` = $updatedQuantity WHERE `project_equipment_id` = $existingEquipmentId";
            $updateResult = mysqli_query($con, $updateQuery);

            if (!$updateResult) {
                echo "Error updating equipment quantity: " . mysqli_error($con);
            }
        } else {
            if ($equipmentQuantity > 0) { // Only insert if quantity is greater than 0
                // Equipment does not exist, insert it as a new entry
                $insertQuery = "INSERT INTO `project_equipment` (project_id, equipment_name, quantity) VALUES ($project_id, '$equipmentName', $equipmentQuantity)";
                $insertResult = mysqli_query($con, $insertQuery);

                if (!$insertResult) {
                    echo "Error inserting new equipment: " . mysqli_error($con);
                }
            }
        }

       // Insert audit trail record for used equipment action
        $login_user_id = $_SESSION['user_id'];
        $action = "Used Equipment";
        $timestamp = date('Y-m-d H:i:s');
        $details = "(Project ID: $project_id, Equipment Name: $equipmentName, Quantity: $equipmentQuantity)";
        $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
        mysqli_query($con, $audit_query);

    }

    // Update the "available" column in the equipment table
    $updateAvailableQuery = "UPDATE equipment SET available = IFNULL(quantity - (SELECT SUM(quantity) FROM project_equipment WHERE project_id = $project_id AND equipment_name = equipment.name), quantity)";
    $updateAvailableResult = mysqli_query($con, $updateAvailableQuery);

    if (!$updateAvailableResult) {
        echo "Error updating equipment availability: " . mysqli_error($con);
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
        width: 100%;
    }

    .equipment-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .equipment-table th,
    .equipment-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ccc;
    }

    .equipment-table th {
        background-color: #f5f5f5;
    }

    .equipment-table input[type="number"] {
        padding: 8px;
        width: 60px;
    }

    .equipment-table button {
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
    <h1>Add Equipment to Project</h1>

    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search for equipment">
    </div>

    <div id="search-results">
        <!-- Display search results here -->
    </div>

    <form id="add-equipment-form" action="add_equipments_to_project.php" method="POST">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

        <table class="equipment-table">
            <thead>
                <tr>
                    <th>Equipment Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Available</th>
                    <th>Quantity to Add</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="equipment-table-body">
                <!-- Display equipment here -->
            </tbody>
        </table>
    </form>

    <script>
        // Function to update the equipment table based on search results
        function updateEquipmentTable(equipment) {
            let tableBody = document.getElementById('equipment-table-body');
            tableBody.innerHTML = '';

            for (let i = 0; i < equipment.length; i++) {
                let equipmentItem = equipment[i];
                let row = document.createElement('tr');
                let nameCell = document.createElement('td');
                let categoryCell = document.createElement('td');
                let quantityCell = document.createElement('td');
                let availableCell = document.createElement('td');
                let quantityToAddCell = document.createElement('td');
                let actionCell = document.createElement('td');
                let quantityInput = document.createElement('input');
                let nameInput = document.createElement('input');
                let addButton = document.createElement('button');

                nameInput.type = 'hidden';
                nameInput.name = `equipment[${equipmentItem.id}][name]`;
                nameInput.value = equipmentItem.name;

                quantityInput.type = 'number';
                quantityInput.name = `equipment[${equipmentItem.id}][quantity]`;
                quantityInput.min = '0';
                quantityInput.max = equipmentItem.available; // Set the max value to the available quantity

                addButton.type = 'button';
                addButton.textContent = 'Add';
                addButton.addEventListener('click', function () {
                    // Get the quantity to add
                    let quantityToAdd = parseInt(quantityInput.value);

                    if (quantityToAdd > 0) {
                        addEquipmentToProject(<?php echo $project_id; ?>, equipmentItem.name, quantityToAdd);
                    }
                });

                nameCell.textContent = equipmentItem.name;
                categoryCell.textContent = equipmentItem.category;
                quantityCell.textContent = equipmentItem.quantity;
                availableCell.textContent = equipmentItem.available;

                quantityToAddCell.appendChild(quantityInput);
                actionCell.appendChild(addButton);

                row.appendChild(nameCell);
                row.appendChild(categoryCell);
                row.appendChild(quantityCell);
                row.appendChild(availableCell);
                row.appendChild(quantityToAddCell);
                row.appendChild(actionCell);
                row.appendChild(nameInput);

                tableBody.appendChild(row);
            }
        }

        function searchEquipment() {
  let input = document.getElementById('search-input').value.trim();
  let xhr = new XMLHttpRequest();

  if (input !== '') {
    xhr.open('GET', `search_equipments_proj.php?search=${input}`, true);
    xhr.onload = function () {
      if (xhr.status === 200) {
        let equipment = JSON.parse(xhr.responseText);
        updateEquipmentTable(equipment);
      }
    };
    xhr.send();
  } else {
    let tableBody = document.getElementById('equipment-table-body');
    tableBody.innerHTML = '';
  }
}

        // Function to add equipment to the project
        function addEquipmentToProject(projectId, equipmentName, quantityToAdd) {
            // Create a form to submit the data
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = 'add_equipments_to_project.php';

            let projectIdInput = document.createElement('input');
            projectIdInput.type = 'hidden';
            projectIdInput.name = 'project_id';
            projectIdInput.value = projectId;

            let equipmentNameInput = document.createElement('input');
            equipmentNameInput.type = 'hidden';
            equipmentNameInput.name = `equipment[${projectId}][name]`;
            equipmentNameInput.value = equipmentName;

            let quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `equipment[${projectId}][quantity]`;
            quantityInput.value = quantityToAdd;

            form.appendChild(projectIdInput);
            form.appendChild(equipmentNameInput);
            form.appendChild(quantityInput);

            document.body.appendChild(form);
            form.submit();
        }

        // Event listener for search input
        let searchInput = document.getElementById('search-input');
        searchInput.addEventListener('input', searchEquipment);
    </script>
</div>
