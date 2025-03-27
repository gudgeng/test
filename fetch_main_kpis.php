

<?php
include 'dbconnect.php';

if (isset($_GET['year'])) {
    $year = intval($_GET['year']);
    $dataType = 'KENAF'; // Adjust this if needed

    $sql = "SELECT DATAID, DATANAME FROM kpi2.kpimaindata WHERE YEAR(DATADATE) = ? AND DATATYPE = ? ORDER BY DATAID ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $year, $dataType);
    $stmt->execute();
    $result = $stmt->get_result();

    $mainKpis = [];
    while ($row = $result->fetch_assoc()) {
        $mainKpis[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode($mainKpis);
}
?>