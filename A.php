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

        h2 {
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

        .back-button {
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

        .back-button:hover {
            background-color: #1e5e28;
        }
    </style>
</head>
<body>
    <h2>KPI All Data</h2>
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
    <div class="table-container">
        <table border="1" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="width: 5%; text-align: center;">Bil</th>
                    <th style="width: 35%;">KPI TAHUN <?= htmlspecialchars($selectedYear) ?></th>
                    <th style="width: 15%; text-align: center;">Sasaran</th>
                    <th style="width: 15%; text-align: center;">Pencapaian Semasa</th>
                    <th style="width: 20%;">Catatan</th>
                    <th style="width: 10%; text-align: center;">Bahagian / Unit</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $bil = 1; // Initialize the serial number globally

                // Loop for KENAF
                if (!empty($allData['KENAF'])) {
                    echo "<tr><td colspan='6' style='text-align: center; font-weight: bold; background-color: #f0f0f0;'>KENAF</td></tr>";
                    foreach ($allData['KENAF'] as $dataId => $data) {
                        echo "<tr class='main-category'>";
                        echo "<td style='text-align: center;'>{$bil}</td>";
                        echo "<td colspan='5'>{$data["DATANAME"]}</td>";
                        echo "</tr>";

                        if (!empty($data["SUBITEMS"])) {
                            foreach ($data["SUBITEMS"] as $sub) {
                                echo "<tr>";
                                echo "<td style='text-align: center;'></td>";
                                echo "<td class='sub-category'>" . htmlspecialchars($sub["SUBNAME"]) . "</td>";
                                echo "<td style='text-align: center;'>" . htmlspecialchars($sub["SASARAN"]) . "</td>";
                                echo "<td style='text-align: center;'>" . htmlspecialchars($sub["PENCAPAIAN"]) . "</td>";
                                echo "<td>" . htmlspecialchars($sub["CATATAN"]) . "</td>";
                                echo "<td style='text-align: center;'>" . htmlspecialchars($sub["JENISUNIT"]) . "</td>";
                                echo "</tr>";
                            }
                        } else { ?>
                            <tr>
                                <td></td>
                                <td class="sub-category" colspan="5" style="text-align: center;">No Sub-KPI Available for <?= htmlspecialchars($monthName) ?></td>
                            </tr>
                        <?php }
                        $bil++;
                    }
                }

                // Add something between KENAF and TEMBAKAU
               // echo "<tr><td colspan='6' style='text-align: center; font-style: italic;'>--- Transition to TEMBAKAU ---</td></tr>";

                // Loop for TEMBAKAU
                if (!empty($allData['TEMBAKAU'])) {
                    echo "<tr><td colspan='6' style='text-align: center; font-weight: bold; background-color: #f0f0f0;'>TEMBAKAU</td></tr>";
                    foreach ($allData['TEMBAKAU'] as $dataId => $data) {
                        echo "<tr class='main-category'>";
                        echo "<td style='text-align: center;'>{$bil}</td>";
                        echo "<td colspan='5'>{$data["DATANAME"]}</td>";
                        echo "</tr>";

                        if (!empty($data["SUBITEMS"])) {
                            foreach ($data["SUBITEMS"] as $sub) {
                                echo "<tr>";
                                echo "<td style='text-align: center;'></td>";
                                echo "<td class='sub-category'>" . htmlspecialchars($sub["SUBNAME"]) . "</td>";
                                echo "<td style='text-align: center;'>" . htmlspecialchars($sub["SASARAN"]) . "</td>";
                                echo "<td style='text-align: center;'>" . htmlspecialchars($sub["PENCAPAIAN"]) . "</td>";
                                echo "<td>" . htmlspecialchars($sub["CATATAN"]) . "</td>";
                                echo "<td style='text-align: center;'>" . htmlspecialchars($sub["JENISUNIT"]) . "</td>";
                                echo "</tr>";
                            }
                        } else { ?>
                            <tr>
                                <td></td>
                                <td class="sub-category" colspan="5" style="text-align: center;">No Sub-KPI Available for <?= htmlspecialchars($monthName) ?></td>
                            </tr>
                        <?php }
                        $bil++;
                    }
                }

                // Add something between TEMBAKAU and PENTADBIRAN
               // echo "<tr><td colspan='6' style='text-align: center; font-style: italic;'>--- Transition to PENTADBIRAN ---</td></tr>";

                // Loop for PENTADBIRAN
                if (!empty($allData['PENTADBIRAN'])) {
                    echo "<tr><td colspan='6' style='text-align: center; font-weight: bold; background-color: #f0f0f0;'>PENTADBIRAN</td></tr>";
                    foreach ($allData['PENTADBIRAN'] as $dataId => $data) {
                        echo "<tr class='main-category'>";
                        echo "<td style='text-align: center;'>{$bil}</td>";
                        echo "<td colspan='5'>{$data["DATANAME"]}</td>";
                        echo "</tr>";

                        if (!empty($data["SUBITEMS"])) {
                            foreach ($data["SUBITEMS"] as $sub) {
                                echo "<tr>";
                                echo "<td style='text-align: center;'></td>";
                                echo "<td class='sub-category'>" . htmlspecialchars($sub["SUBNAME"]) . "</td>";
                                echo "<td style='text-align: center;'>" . htmlspecialchars($sub["SASARAN"]) . "</td>";
                                echo "<td style='text-align: center;'>" . htmlspecialchars($sub["PENCAPAIAN"]) . "</td>";
                                echo "<td>" . htmlspecialchars($sub["CATATAN"]) . "</td>";
                                echo "<td style='text-align: center;'>" . htmlspecialchars($sub["JENISUNIT"]) . "</td>";
                                echo "</tr>";
                            }
                        } else { ?>
                            <tr>
                                <td></td>
                                <td class="sub-category" colspan="5" style="text-align: center;">No Sub-KPI Available for <?= htmlspecialchars($monthName) ?></td>
                            </tr>
                        <?php }
                        $bil++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <form method="POST" action="export_excel.php" style="display: inline-block;">
            <input type="hidden" name="year" value="<?= htmlspecialchars($selectedYear) ?>">
            <input type="hidden" name="data_type" value="<?= htmlspecialchars($dataType) ?>">
            <button type="submit" class="add-data-button">Export All Months to Excel</button>
        </form>
        <form method="POST" action="export_csv.php" style="display: inline-block;">
            <input type="hidden" name="year" value="<?= htmlspecialchars($selectedYear) ?>">
            <input type="hidden" name="month" value="<?= htmlspecialchars($selectedMonth) ?>">
            <button type="submit" class="add-data-button">Export to CSV</button>
        </form>
        <form method="POST" action="export_csv_all_months.php" style="display: inline-block;">
            <input type="hidden" name="year" value="<?= htmlspecialchars($selectedYear) ?>">
            <input type="hidden" name="data_type" value="<?= htmlspecialchars($dataType) ?>">
            <button type="submit" class="add-data-button">Export All Months to CSV</button>
        </form>
        <form method="POST" action="generate_pdf.php" style="display: inline-block;">
            <input type="hidden" name="year" value="<?= htmlspecialchars($selectedYear) ?>">
            <input type="hidden" name="month" value="<?= htmlspecialchars($selectedMonth) ?>">
            <button type="submit" class="add-data-button">Generate PDF</button>
        </form>
    </div>
    <a href="homepage.html" class="back-button">Kembali</a>
</body>
</html>

<?php $conn->close(); ?>
