<?php
session_start();
include 'dbconnect.php';

// Ensure the user is logged in
if (!isset($_SESSION['USERID'])) {
    header("Location: login.php");
    exit();
}

// Retrieve session variables
$loggedInUnit = $_SESSION['UNIT'];
$userId = $_SESSION['USERID'];
$isAdmin = ($loggedInUnit === 'ADMIN');

// Get the current year and month
$currentYear = date('Y');
$currentMonth = date('n');

// Get the selected year and month from the search bar or default to the current year and month
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : $currentYear;
$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : $currentMonth;

// Determine the month name
$months = [
    1 => 'JANUARY', 2 => 'FEBRUARY', 3 => 'MARCH', 4 => 'APRIL',
    5 => 'MAY', 6 => 'JUNE', 7 => 'JULY', 8 => 'AUGUST',
    9 => 'SEPTEMBER', 10 => 'OCTOBER', 11 => 'NOVEMBER', 12 => 'DECEMBER'
];
$monthName = $months[$selectedMonth];

// Define the data types
$dataTypes = ['KENAF', 'PENTADBIRAN', 'TEMBAKAU'];

// Fetch KPI Data for all types
$allData = [];
foreach ($dataTypes as $dataType) {
    $sql = "SELECT kpi2.kpimaindata.DATAID, kpi2.kpimaindata.DATANAME, kpi2.kpimaindata.DATADATE, 
                   kpi2.kpisubdata.SUBNAME, kpi2.kpisubdata.SASARAN, kpi2.kpisubdata.PENCAPAIAN, 
                   kpi2.kpisubdata.CATATAN, kpi2.kpisubdata.JENISUNIT, kpi2.kpisubdata.SUBID, kpi2.kpisubdata.DATAMONTH
            FROM kpi2.kpimaindata
            LEFT JOIN kpi2.kpisubdata 
            ON kpi2.kpimaindata.DATAID = kpi2.kpisubdata.DATAID AND kpi2.kpisubdata.DATAMONTH = ?
            WHERE kpi2.kpimaindata.DATATYPE = ? AND YEAR(kpi2.kpimaindata.DATADATE) = ?
            ORDER BY kpi2.kpimaindata.DATAID, kpi2.kpisubdata.DATAMONTH";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $selectedMonth, $dataType, $selectedYear);
    $stmt->execute();
    $result = $stmt->get_result();

    $groupedData = [];
    while ($row = $result->fetch_assoc()) {
        $dataId = $row["DATAID"];
        if (!isset($groupedData[$dataId])) {
            $groupedData[$dataId] = [
                "DATANAME" => $row["DATANAME"],
                "SUBITEMS" => []
            ];
        }
        if (!empty($row["SUBNAME"])) {
            $groupedData[$dataId]["SUBITEMS"][] = $row;
        }
    }
    $stmt->close();
    $allData[$dataType] = $groupedData;
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI All Data</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
        }

        h2, h3 {
            text-align: center;
            color: #2f813d;
        }

        .table-container {
            margin: 20px auto;
            padding: 10px;
            max-width: 95%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #2f813d;
            color: white;
            font-weight: bold;
        }

        .main-category {
            font-weight: bold;
            background-color: #d4edda;
        }

        tr:nth-child(even):not(.main-category) {
            background-color: rgba(250, 243, 243, 0.93);
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .sub-category {
            padding-left: 20px;
        }

        .back-button, .add-data-button, button {
            display: inline-block;
            margin: 10px auto;
            padding: 10px 20px;
            background-color: #2f813d;
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .back-button:hover, .add-data-button:hover, button:hover {
            background-color: #1e5e28;
        }
    </style>
</head>
<body>
    <h2>KPI All Data</h2>

    <!-- Search Bar for Year and Month -->
    <div class="search-bar" style="text-align: center; margin-bottom: 20px;">
        <form method="GET" action="">
            <input type="number" name="year" placeholder="Enter Year (e.g., 2025)" min="2000" max="2100" value="<?= htmlspecialchars($selectedYear) ?>" required>
            <select name="month" required>
                <option value="" disabled>Select Month</option>
                <?php foreach ($months as $key => $month): ?>
                    <option value="<?= $key ?>" <?= $key == $selectedMonth ? 'selected' : '' ?>>
                        <?= $month ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Display Data for Each Type -->
    <?php 
    $bil = 1; // Initialize the serial number globally

    foreach ($allData as $dataType => $groupedData): ?>
        <div class="table-container">
            <h3 style="text-align: center; color: #2f813d;"><?= htmlspecialchars($dataType) ?> </h3>
            <table border="1" style="width: 100%; border-collapse: collapse;">
                <?php// if ($dataType === 'KENAF'): // Only display headers for the first data type ?>
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th>Bil</th>
                            <th>KPI TAHUN <?= htmlspecialchars($selectedYear) ?></th>
                            <th>Sasaran</th>
                            <th>Pencapaian Semasa</th>
                            <th>Catatan</th>
                            <th>Bahagian / Unit</th>
                        </tr>
                    </thead>
                <?php// endif; ?>
                <tbody>
                    <?php 
                    if (empty($groupedData)) { ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: red;">No data available for <?= htmlspecialchars($monthName) ?> <?= htmlspecialchars($selectedYear) ?>.</td>
                        </tr>
                    <?php } else {
                        foreach ($groupedData as $dataId => $data) { 
                            // Main KPI Row
                            echo "<tr style='background-color: #d4edda; font-weight: bold;'>";
                            echo "<td>{$bil}</td>";
                            echo "<td colspan='5'>{$data["DATANAME"]}</td>";
                            echo "</tr>";

                            // Check if there are Sub-KPIs for this Main KPI
                            if (!empty($data["SUBITEMS"])) {
                               // $subBil = 'a'; // Initialize subcategory numbering
                                foreach ($data["SUBITEMS"] as $sub) { ?>
                                    <tr>
                                        <td style="text-align: center;"><?//= $bil . '.' . $subBil ?></td>
                                        <td class="sub-category"><?= htmlspecialchars($sub["SUBNAME"]) ?></td>
                                        <td><?= htmlspecialchars($sub["SASARAN"]) ?></td>
                                        <td><?= htmlspecialchars($sub["PENCAPAIAN"]) ?></td>
                                        <td><?= htmlspecialchars($sub["CATATAN"]) ?></td>
                                        <td><?= htmlspecialchars($sub["JENISUNIT"]) ?></td>
                                    </tr>
                                    <?php 
                                    //$subBil++; // Increment subcategory numbering
                                }
                            } else { ?>
                                <tr>
                                    <td></td>
                                    <td class="sub-category" colspan="5" style="text-align: center;">No Sub-KPI Available for <?= htmlspecialchars($monthName) ?></td>
                                </tr>
                            <?php }
                            $bil++; // Increment the main serial number
                        }
                    } ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>

    <a href="homepage.html" class="back-button">Kembali</a>
</body>
</html>

<?php $conn->close(); ?>
