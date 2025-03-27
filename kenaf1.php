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
$userId = $_SESSION['USERID']; // USERID from session
$userUnit = $_SESSION['UNIT']; // UNIT from session
$isAdmin = ($loggedInUnit === 'ADMIN'); // Check if the user is an admin
// Set predefined data type for Kenaf
$dataType = 'KENAF';

// Get the selected year from the search bar (default to current year)
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Fetch KPI Data filtered by year, including KPIs without sub-KPIs
$sql = "SELECT kpi1.kpimaindata.DATAID, kpi1.kpimaindata.DATANAME, kpi1.kpimaindata.DATADATE, 
               kpi1.kpisubdata.SUBNAME, kpi1.kpisubdata.SASARAN, kpi1.kpisubdata.PENCAPAIAN, 
               kpi1.kpisubdata.CATATAN, kpi1.kpisubdata.JENISUNIT, kpi1.kpisubdata.SUBID
        FROM kpi1.kpimaindata
        LEFT JOIN kpi1.kpisubdata ON kpi1.kpimaindata.DATAID = kpi1.kpisubdata.DATAID
        WHERE kpi1.kpimaindata.DATATYPE = ? AND YEAR(kpi1.kpimaindata.DATADATE) = ?
        ORDER BY kpi1.kpimaindata.DATAID, kpi1.kpisubdata.SUBNAME";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $dataType, $selectedYear);
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

// Fetch main KPIs for the dropdown in the Add Sub-KPI modal
$mainKpis = [];
$mainKpiSql = "SELECT DATAID, DATANAME FROM kpi1.kpimaindata WHERE DATATYPE = ?";
$stmt = $conn->prepare($mainKpiSql);
$stmt->bind_param("s", $dataType);
$stmt->execute();
$mainKpiResult = $stmt->get_result();
while ($row = $mainKpiResult->fetch_assoc()) {
    $mainKpis[] = $row;
}
$stmt->close();

// Fetch Main KPI IDs and Names sorted by DATAID
$mainKpis = [];
$sqlMainKpi = "SELECT DATAID, DATANAME FROM kpi1.kpimaindata ORDER BY DATAID ASC";
$stmtMainKpi = $conn->prepare($sqlMainKpi);
$stmtMainKpi->execute();
$resultMainKpi = $stmtMainKpi->get_result();
while ($row = $resultMainKpi->fetch_assoc()) {
    $mainKpis[] = $row;
}
$stmtMainKpi->close();

// Handle adding new main KPI data
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_main_kpi'])) {
    $dataName = $_POST['DATANAME'];
    $dataDate = $_POST['DATADATE'];

    // Check if DATANAME already exists
    $checkSql = "SELECT DATAID FROM kpi1.kpimaindata WHERE DATANAME = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $dataName);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // DATANAME already exists
        echo "<script>alert('Error: KPI Name already exists. Please use a different name.');</script>";
    } else {
        // Insert new DATANAME
        $stmt->close();
        $insertSql = "INSERT INTO kpi1.kpimaindata (DATANAME, DATADATE, DATATYPE, USERID) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("sssi", $dataName, $dataDate, $dataType, $userId);

        if ($stmt->execute()) {
            echo "<script>alert('Main KPI data added successfully.'); </script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

// Handle adding new sub-KPI data
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_sub_kpi'])) {
    $dataID = $_POST['DATAID'];
    $dataYear = $_POST['DATAYEAR'];
    $subName = $_POST['SUBNAME'];
    $sasaran = $_POST['SASARAN'];
    $pencapaian = $_POST['PENCAPAIAN'];
    $catatan = $_POST['CATATAN'];
    $jenisUnit = $_POST['JENISUNIT'];

    // Validate the year
    if (!is_numeric($dataYear) || $dataYear < 2000 || $dataYear > 2100) {
        echo "<script>alert('Error: Invalid year. Please enter a valid year between 2000 and 2100.');</script>";
        exit();
    }

    // Check if the main KPI exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM kpi1.kpimaindata WHERE DATAID = ? AND YEAR(DATADATE) = ?");
    $stmt->bind_param("ii", $dataID, $dataYear);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count === 0) {
        echo "<script>alert('Error: Invalid Main KPI ID or Year. Please ensure the Main KPI exists for the selected year.');</script>";
    } else {
        // Insert the Sub-KPI data
        $stmt = $conn->prepare("INSERT INTO kpi1.kpisubdata (DATAID, SUBNAME, SASARAN, PENCAPAIAN, CATATAN, JENISUNIT, USERID) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssi", $dataID, $subName, $sasaran, $pencapaian, $catatan, $jenisUnit, $userId);

        if ($stmt->execute()) {
            echo "<script>alert('Sub-KPI added successfully.'); window.location.href = window.location.href;</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle form submission for updating data
$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['add_data']) && !isset($_POST['add_main_kpi'])) {
    $subid = $_POST['subid'];
    $pencapaian = $_POST['pencapaian'];
    $catatan = $_POST['catatan'];

    // Update the database
    $updateSql = "UPDATE kpi1.kpisubdata SET PENCAPAIAN = ?, CATATAN = ? WHERE SUBID = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssi", $pencapaian, $catatan, $subid);

    if ($stmt->execute()) {
        $successMessage = "Data successfully saved!";
        echo "<script>alert('Data successfully saved!'); window.location.href = window.location.href;</script>";
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Kenaf</title>
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

        .success-message {
            color: green;
            font-size: 16px;
            margin-bottom: 20px;
            text-align: center;
        }

        .back-button, .add-data-button, .year-button, button {
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

        .back-button:hover, .add-data-button:hover, .year-button:hover, button:hover {
            background-color: #1e5e28;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .modal-content h2 {
            margin-bottom: 20px;
        }

        .year-button {
            display: inline-block;
            margin: 5px;
            padding: 10px 20px;
            background-color: #2f813d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .year-button:hover {
            background-color: #1e5e28;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
            }

            table {
                font-size: 14px;
            }

            .year-button {
                font-size: 12px;
                padding: 8px 15px;
            }
        }

        /* Add styles for the search bar */
        .search-bar {
            margin: 20px auto;
            text-align: center;
        }

        .search-bar input[type="number"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
        }

        .search-bar button {
            padding: 10px 20px;
            background-color: #2f813d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .search-bar button:hover {
            background-color: #1e5e28;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .modal-content h2 {
            margin-bottom: 20px;
        }

        .modal-content input[type="text"],
        .modal-content input[type="number"],
        .modal-content textarea,
        .modal-content select {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .modal-content button {
            padding: 10px 20px;
            background-color: #2f813d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .modal-content button:hover {
            background-color: #1e5e28;
        }

        .modal-content select {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .modal-content input[type="number"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
    </style>
    <script>
        function enableEdit(button) {
            const row = button.closest('tr');
            const pencapaianView = row.querySelector('.pencapaian-view');
            const pencapaianInput = row.querySelector('input[name="pencapaian"]');
            const catatanView = row.querySelector('.catatan-view');
            const catatanInput = row.querySelector('input[name="catatan"]');
            const saveButton = row.querySelector('button[type="submit"]');

            pencapaianView.style.display = 'none';
            pencapaianInput.style.display = 'inline-block';
            pencapaianInput.removeAttribute('readonly');

            catatanView.style.display = 'none';
            catatanInput.style.display = 'inline-block';
            catatanInput.removeAttribute('readonly');

            button.style.display = 'none';
            saveButton.style.display = 'inline-block';
        }

        function confirmSave(event) {
            if (!confirm("Are you sure you want to save the changes?")) {
                event.preventDefault();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            
            // Modal for Selecting Year
            const yearModal = document.getElementById("yearModal");
            const selectYearButton = document.getElementById("selectYearButton");
            const closeModal = document.querySelector(".close");
            const yearList = document.getElementById("yearList");
            const yearInput = document.getElementById("DATADATE");

            // Generate a list of years dynamically
            const currentYear = new Date().getFullYear();
            const untilYear = currentYear + 20;
            for (let year = currentYear; year <= untilYear; year++) {
                const yearButton = document.createElement("button");
                yearButton.textContent = year;
                yearButton.classList.add("year-button");
                yearButton.onclick = function () {
                    yearInput.value = year; // Set the selected year in the input field
                    yearModal.style.display = "none"; // Close the modal
                };
                yearList.appendChild(yearButton);
            }

            // Show the modal when the "Select Year" button is clicked
            selectYearButton.onclick = function () {
                yearModal.style.display = "block";
            };

            // Close the modal when the "x" button is clicked
            closeModal.onclick = function () {
                yearModal.style.display = "none";
            };

            // Close the modal when clicking outside the modal content
            window.onclick = function (event) {
                if (event.target === yearModal) {
                    yearModal.style.display = "none";
                }
            };
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Modal for Adding Main KPI
            const addMainKPIModal = document.getElementById("addMainKPIModal");
            const addMainKPIButton = document.getElementById("addMainKPIButton");
            const closeMainKPIModal = addMainKPIModal.querySelector(".close");

            // Show the modal when the "Add Main KPI" button is clicked
            addMainKPIButton.onclick = function () {
                addMainKPIModal.style.display = "block";
            };

            // Close the modal when the "x" button is clicked
            closeMainKPIModal.onclick = function () {
                addMainKPIModal.style.display = "none";
            };

            // Close the modal when clicking outside the modal content
            window.onclick = function (event) {
                if (event.target === addMainKPIModal) {
                    addMainKPIModal.style.display = "none";
                }
            };
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Modal for Adding Sub-KPI
            const addSubKPIModal = document.getElementById("addSubKPIModal");
            const addSubKPIButton = document.getElementById("addSubKPIButton");
            const closeSubKPIModal = addSubKPIModal.querySelector(".close");

            // Show the modal when the "Add Sub-KPI" button is clicked
            addSubKPIButton.onclick = function () {
                addSubKPIModal.style.display = "block";
            };

            // Close the modal when the "x" button is clicked
            closeSubKPIModal.onclick = function () {
                addSubKPIModal.style.display = "none";
            };

            // Close the modal when clicking outside the modal content
            window.onclick = function (event) {
                if (event.target === addSubKPIModal) {
                    addSubKPIModal.style.display = "none";
                }
            };
        });

        document.addEventListener('DOMContentLoaded', function () {
            const yearDropdown = document.getElementById('DATAYEAR');
            const mainKpiDropdown = document.getElementById('DATAID');

            yearDropdown.addEventListener('change', function () {
                const selectedYear = yearDropdown.value;

                // Clear existing options in the Main KPI dropdown
                mainKpiDropdown.innerHTML = '<option value="" disabled selected>Select Main KPI</option>';

                if (selectedYear) {
                    // Enable the Main KPI dropdown
                    mainKpiDropdown.disabled = false;

                    // Fetch Main KPIs for the selected year via AJAX
                    fetch(`fetch_main_kpis.php?year=${selectedYear}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                data.forEach(kpi => {
                                    const option = document.createElement('option');
                                    option.value = kpi.DATAID;
                                    option.textContent = `${kpi.DATAID} - ${kpi.DATANAME}`;
                                    mainKpiDropdown.appendChild(option);
                                });
                            } else {
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = 'No Main KPI available for this year';
                                option.disabled = true;
                                mainKpiDropdown.appendChild(option);
                            }
                        })
                        .catch(error => console.error('Error fetching Main KPIs:', error));
                } else {
                    // Disable the Main KPI dropdown if no year is selected
                    mainKpiDropdown.disabled = true;
                }
            });
        });
    </script>
</head>
<body>
    <h2>KPI Kenaf</h2>

    <!-- Search Bar for Year -->
    <div class="search-bar">
        <form method="GET" action="">
            <input type="number" name="year" placeholder="Enter Year (e.g., 2025)" min="0" max="2100" value="<?= htmlspecialchars($selectedYear) ?>" required>
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Add Main KPI Button -->
    <?php if ($isAdmin): ?>
        <button id="addMainKPIButton" class="add-data-button">Add Main KPI</button>
    <?php endif; ?>

    <!-- Add Sub-KPI Button -->
    <?php if ($isAdmin): ?>
        <button id="addSubKPIButton" class="add-data-button">Add Sub-KPI</button>
    <?php endif; ?>

    <!-- Modal for Adding Main KPI -->
    <div id="addMainKPIModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Main KPI</h2>
            <form method="POST">
                <input type="text" name="DATANAME" placeholder="KPI Name" required>
                <input type="number" id="DATADATE" name="DATADATE" placeholder="Year (e.g., 2025)" min="2025" max="2030" required readonly>
                <button type="button" id="selectYearButton">Select Year</button>
                <button type="submit" name="add_main_kpi">Submit</button>
            </form>
        </div>
    </div>

    <!-- Add Main KPI Form -->
    

    <!-- Modal for Selecting Year -->
    <div id="yearModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Select Year</h2>
            <div id="yearList">
                <!-- Years will be dynamically generated here -->
            </div>
        </div>
    </div>

    <!-- Modal for Adding Sub-KPI -->
    <div id="addSubKPIModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Sub-KPI</h2>
            <form method="POST" action="">
                <label for="DATAYEAR">Select Year:</label>
                <select name="DATAYEAR" id="DATAYEAR" required>
                    <option value="" disabled selected>Select Year</option>
                    <?php for ($year = 2000; $year <= date('Y') + 10; $year++): ?>
                        <option value="<?= $year ?>"><?= $year ?></option>
                    <?php endfor; ?>
                </select>

                <label for="DATAID">Select Main KPI:</label>
                <select name="DATAID" id="DATAID" required disabled>
                    <option value="" disabled selected>Select Main KPI</option>
                    <!-- Main KPI options will be dynamically populated -->
                </select>

                <input type="text" name="SUBNAME" placeholder="Sub KPI Name" required>
                <input type="text" name="SASARAN" placeholder="Target" required>
                <input type="text" name="PENCAPAIAN" placeholder="Achievement" required>
                <textarea name="CATATAN" placeholder="Notes"></textarea>
                <select name="JENISUNIT" required>
                    <option value="BPP">BPP</option>
                    <option value="BKK">BKK</option>
                    <option value="UUU">UUU</option>
                    <option value="BRD">BRD</option>
                    <option value="ADMIN">ADMIN</option>
                </select>
                <button type="submit" name="add_sub_kpi">Submit</button>
            </form>
        </div>
    </div>

    <!-- Display KPI Data -->
    <div class="table-container">
        <table>
            <tr>
                <th>Bil</th>
                <th>KPI TAHUN <?= htmlspecialchars($selectedYear) ?></th>
                <th>Sasaran</th>
                <th>Pencapaian Semasa</th>
                <th>Catatan</th>
                <th>Bahagian / Unit</th>
                <th>Tindakan</th>
            </tr>
            <?php 
            if (empty($groupedData)) { ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: red;">No data available for the selected year.</td>
                </tr>
            <?php } else {
                $bil = 1;
                foreach ($groupedData as $dataId => $data) { 
                    echo "<tr class='main-category'>";
                    echo "<td>{$bil}</td>";
                    echo "<td colspan='6'>{$data["DATANAME"]}</td>";
                    echo "</tr>";
                    if (!empty($data["SUBITEMS"])) {
                        foreach ($data["SUBITEMS"] as $sub) { ?>
                            <tr>
                                <form method="POST" action="">
                                    <td></td>
                                    <td class="sub-category"><?= htmlspecialchars($sub["SUBNAME"]) ?></td>
                                    <td><?= htmlspecialchars($sub["SASARAN"]) ?></td>
                                    <td>
                                        <span class="pencapaian-view"><?= htmlspecialchars($sub["PENCAPAIAN"]) ?></span>
                                        <input type="text" name="pencapaian" value="<?= htmlspecialchars($sub["PENCAPAIAN"]) ?>" style="display: none;" readonly>
                                    </td>
                                    <td>
                                        <span class="catatan-view"><?= htmlspecialchars($sub["CATATAN"]) ?></span>
                                        <input type="text" name="catatan" value="<?= htmlspecialchars($sub["CATATAN"]) ?>" style="display: none;" readonly>
                                    </td>
                                    <td><?= htmlspecialchars($sub["JENISUNIT"]) ?></td>
                                    <td>
                                        <input type="hidden" name="subid" value="<?= $sub["SUBID"] ?>">
                                        <?php if ($isAdmin || $sub["JENISUNIT"] == $loggedInUnit) { ?>
                                            <button type="button" onclick="enableEdit(this)">Edit</button>
                                            <button type="submit" onclick="confirmSave(event)" style="display: none;">Simpan</button>
                                        <?php } ?>
                                    </td>
                                </form>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td></td>
                            <td class="sub-category" colspan="6" style="text-align: center;">No Sub-KPI Available</td>
                        </tr>
                    <?php }
                    $bil++;
                }
            } ?>
        </table>
    </div>
    <a href="homepage.html" class="back-button">Kembali</a>
</body>
</html>

<?php $conn->close(); ?>