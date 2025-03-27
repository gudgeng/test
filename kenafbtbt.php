<?php
session_start();
include 'dbconnect.php';

// Assume user is logged in and session contains UNIT info
$loggedInUnit = $_SESSION['UNIT'];

$sql = "SELECT kpi1.kpimaindata.DATAID, kpi1.kpimaindata.DATANAME, kpi1.kpimaindata.DATATYPE, kpi1.kpisubdata.SUBNAME, kpi1.kpisubdata.SASARAN, kpi1.kpisubdata.PENCAPAIAN, kpi1.kpisubdata.CATATAN, kpi1.kpisubdata.JENISUNIT, kpi1.kpisubdata.SUBID
        FROM kpi1.kpimaindata
        JOIN kpi1.kpisubdata ON kpi1.kpimaindata.DATAID = kpi1.kpisubdata.DATAID
        WHERE kpi1.kpimaindata.DATATYPE = 'KENAF' 
        ORDER BY kpi1.kpimaindata.DATAID, kpi1.kpisubdata.SUBNAME";

$result = $conn->query($sql);

$groupedData = [];
while ($row = $result->fetch_assoc()) {
    $groupedData[$row["DATAID"]]["DATANAME"] = $row["DATANAME"];
    $groupedData[$row["DATAID"]]["SUBITEMS"][] = $row;
}

// Handle form submission
$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subid = $_POST['subid'];
    $pencapaian = $_POST['pencapaian'];
    $catatan = $_POST['catatan'];

    // Update the database
    $updateSql = "UPDATE kpi1.kpisubdata SET PENCAPAIAN = ?, CATATAN = ? WHERE SUBID = ? AND JENISUNIT = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssis", $pencapaian, $catatan, $subid, $loggedInUnit);

    if ($stmt->execute()) {
        $successMessage = "Data successfully saved!";
        echo "<script>window.onload = function() { alert('Data successfully saved!'); }</script>";
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
            background-color: #d4edda; /*  #d4edda Light green for main categories */
        }

        tr:nth-child(even):not(.main-category) {
            background-color:rgba(250, 243, 243, 0.93); /* Light gray for even rows */
        }

        tr:hover {
            background-color: #f1f1f1; /* Highlight color when hovering */
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

        .back-button {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #2f813d; /* Green background */
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #1e5e28; /* Darker green on hover */
        }
    </style>
    <script>
        function enableEdit(button) {
            const row = button.closest('tr'); // Get the current row
            const pencapaianView = row.querySelector('.pencapaian-view'); // Get the span for Pencapaian
            const pencapaianInput = row.querySelector('input[name="pencapaian"]'); // Get the input for Pencapaian
            const catatanView = row.querySelector('.catatan-view'); // Get the span for Catatan
            const catatanInput = row.querySelector('input[name="catatan"]'); // Get the input for Catatan
            const saveButton = row.querySelector('button[type="submit"]'); // Get the save button

            // Hide the spans and show the inputs
            pencapaianView.style.display = 'none';
            pencapaianInput.style.display = 'inline-block';
            pencapaianInput.removeAttribute('readonly');

            catatanView.style.display = 'none';
            catatanInput.style.display = 'inline-block';
            catatanInput.removeAttribute('readonly');

            // Show the save button and hide the edit button
            button.style.display = 'none';
            saveButton.style.display = 'inline-block';
        }

        function confirmSave(event) {
            if (!confirm("Are you sure you want to save the changes?")) {
                event.preventDefault(); // Prevent form submission if the user cancels
            }
        }
    </script>
</head>
<body>
    <h2>KPI Kenaf</h2>

    <!-- Display success message -->
    <?php if (!empty($successMessage)): ?>
        <p class="success-message"><?= htmlspecialchars($successMessage) ?></p>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <tr>
                <th>Bil</th>
                <th>KPI TAHUN 2024</th>
                <th>Sasaran</th>
                <th>Pencapaian Semasa</th>
                <th>Catatan</th>
                <th>Bahagian / Unit</th>
                <th>Tindakan</th>
            </tr>
            <?php 
            $bil = 1;
            foreach ($groupedData as $dataId => $data) { 
                echo "<tr class='main-category'>";
                echo "<td>{$bil}</td>";
                echo "<td colspan='6'>{$data["DATANAME"]}</td>";
                echo "</tr>";
                foreach ($data["SUBITEMS"] as $sub) { ?>
                    <tr>
                        <form method="POST" action="">
                            <td></td>
                            <td class="sub-category"><?= htmlspecialchars($sub["SUBNAME"]) ?></td>
                            <td><?= htmlspecialchars($sub["SASARAN"]) ?></td>
                            <td>
                                <?php if ($sub["JENISUNIT"] == $loggedInUnit) { ?>
                                    <span class="pencapaian-view"><?= htmlspecialchars($sub["PENCAPAIAN"]) ?></span>
                                    <input type="text" name="pencapaian" value="<?= htmlspecialchars($sub["PENCAPAIAN"]) ?>" style="display: none;" readonly>
                                <?php } else { ?>
                                    <?= htmlspecialchars($sub["PENCAPAIAN"]) ?>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($sub["JENISUNIT"] == $loggedInUnit) { ?>
                                    <span class="catatan-view"><?= htmlspecialchars($sub["CATATAN"]) ?></span>
                                    <input type="text" name="catatan" value="<?= htmlspecialchars($sub["CATATAN"]) ?>" style="display: none;" readonly>
                                <?php } else { ?>
                                    <?= htmlspecialchars($sub["CATATAN"]) ?>
                                <?php } ?>
                            </td>
                            <td><?= htmlspecialchars($sub["JENISUNIT"]) ?></td>
                            <td>
                                <?php if ($sub["JENISUNIT"] == $loggedInUnit) { ?>
                                    <input type="hidden" name="subid" value="<?= $sub["SUBID"] ?>">
                                    <button type="button" onclick="enableEdit(this)">Edit</button>
                                    <button type="submit" onclick="confirmSave(event)" style="display: none;">Simpan</button>
                                <?php } ?>
                            </td>
                        </form>
                    </tr>
                <?php }
                $bil++;
            } ?>
        </table>
    </div>
    <a href="homepage.html" class="back-button">Kembali</a>
</body>
</html>

<?php $conn->close(); ?>
