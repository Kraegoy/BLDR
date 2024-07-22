<?php
session_start();

require_once("connection.php");
require_once("functions.php");
require_once('vendor/autoload.php');

use Dompdf\Dompdf;

// Check if project ID is set and fetch project details from the database
$project_id = mysqli_real_escape_string($con, $_POST['project_id']);

if (isset($_POST['project_id'])) {
    $project_id = $_POST['project_id'];
    $query = "SELECT * FROM project WHERE project_id = $project_id AND status = 'in_progress'";
    $result = mysqli_query($con, $query);
    if (!$result) {
        echo "Error: " . mysqli_error($con);
    } else {
        $row = mysqli_fetch_assoc($result);
        $project_name = $row['project_name'];
        $project_description = $row['project_description'];
        $client_name = $row['client_name'];
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];
        $project_manager = $row['project_manager'];
        $project_team = $row['project_team'];
        $budget = $row['budget'];
        $actual_cost = $row['actual_cost'];
        $status = $row['status'];
        $location = $row['location'];

        // Fetch materials related to the project
        $materialQuery = "SELECT name, quantity, price, total_price FROM material WHERE project_id = $project_id";
        $materialResult = mysqli_query($con, $materialQuery);
        $materials = '';
        if ($materialResult) {
            while ($materialRow = mysqli_fetch_assoc($materialResult)) {
                $materials .= "<tr>";
                $materials .= "<td>{$materialRow['name']}</td>";
                $materials .= "<td>{$materialRow['quantity']}</td>";
                $materials .= "<td>{$materialRow['price']}</td>";
                $materials .= "<td>{$materialRow['total_price']}</td>";
                $materials .= "</tr>";
            }
        }

        // Fetch equipment related to the project
        $equipmentQuery = "SELECT equipment_name, quantity FROM project_equipment WHERE project_id = $project_id";
        $equipmentResult = mysqli_query($con, $equipmentQuery);
        $equipment = '';
        if ($equipmentResult) {
            while ($equipmentRow = mysqli_fetch_assoc($equipmentResult)) {
                $equipment .= "<tr>";
                $equipment .= "<td>{$equipmentRow['equipment_name']}</td>";
                $equipment .= "<td>{$equipmentRow['quantity']}</td>";
                $equipment .= "</tr>";
            }
        }
    }
}

if (isset($_POST['project_id'])) {
    $dompdf = new Dompdf();
    $html = '
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 5px;
            border: 1px solid #000;
            text-align: center;

        }

        .header {
            background-color: #000;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .footer {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: center;
        }
    </style>
    <div class="header">
        <h1>Project Information</h1>
    </div>
    <div class="content">
        <table>
            <tr>
                <th>Project Name</th>
                <td>' . $project_name . '</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>' . $project_description . '</td>
            </tr>
            <tr>
                <th>Client Name</th>
                <td>' . $client_name . '</td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>' . $start_date . '</td>
            </tr>
            <tr>
                <th>End Date</th>
                <td>' . $end_date . '</td>
            </tr>
            <tr>
                <th>Project Manager</th>
                <td>' . $project_manager . '</td>
            </tr>
            <tr>
                <th>Project Team</th>
                <td>' . $project_team . '</td>
            </tr>
            <tr>
                <th>Budget</th>
                <td>'. $budget . '</td>
            </tr>
            <tr>
                <th>Actual Cost</th>
                <td> '. $actual_cost . '</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>' . $status . '</td>
            </tr>
            <tr>
                <th>Location</th>
                <td>' . $location . '</td>
            </tr>
        </table>
        <h3>Materials</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
            </tr>
            ' . $materials . '
        </table>
        <h3>Equipment</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Quantity</th>
            </tr>
            ' . $equipment . '
        </table>
    </div>
    <div class="footer">
        <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    </div>
    ';
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $output = $dompdf->output();

    file_put_contents('project_information.pdf', $output);

    // Redirect to the PDF
    header('Location: project_information.pdf');
    exit();
}
