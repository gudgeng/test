<?php
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedYear = intval($_POST['year']);
    $selectedMonth = intval($_POST['month']);

    // Define the months array
    $months = [
        1 => 'JANUARY', 2 => 'FEBRUARY', 3 => 'MARCH', 4 => 'APRIL',
        5 => 'MAY', 6 => 'JUNE', 7 => 'JULY', 8 => 'AUGUST',
        9 => 'SEPTEMBER', 10 => 'OCTOBER', 11 => 'NOVEMBER', 12 => 'DECEMBER'
    ];
    $monthName = $months[$selectedMonth];

    // Define the data types to include
    $dataTypes = ['KENAF', 'PENTADBIRAN', 'TEMBAKAU'];

    // Prepare CSV file
    $filename = "KPI_LKTN_{$selectedYear}_{$monthName}.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Write the title
    fputcsv($output, ["KPI LKTN TAHUN {$selectedYear} BULAN {$monthName}"]);
    fputcsv($output, []); // Empty row for spacing

    foreach ($dataTypes as $dataType) {
        // Write the data type as a title
        fputcsv($output, [$dataType]);
        fputcsv($output, ['Bil', 'Main KPI', '', 'Sasaran', 'Pencapaian Semasa', 'Catatan', 'Bahagian/Unit']);

        // Fetch main KPIs first
        $sql = "SELECT DISTINCT m.DATAID, m.DATANAME 
                FROM kpi2.kpimaindata m
                WHERE m.DATATYPE = ? AND YEAR(m.DATADATE) = ?
                ORDER BY m.DATAID";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $dataType, $selectedYear);
        $stmt->execute();
        $mainResult = $stmt->get_result();

        $bil = 1;
        while ($main = $mainResult->fetch_assoc()) {
            // Write main KPI
            fputcsv($output, [$bil, $main['DATANAME'], '', '', '', '', '']);
            
            // Fetch and write sub-KPIs for this main KPI
            $subSql = "SELECT s.SUBNAME, s.SASARAN, s.PENCAPAIAN, s.CATATAN, s.JENISUNIT
                       FROM kpi2.kpisubdata s
                       WHERE s.DATAID = ? AND s.DATAMONTH = ?
                       ORDER BY s.SUBID";
            
            $subStmt = $conn->prepare($subSql);
            $subStmt->bind_param("ii", $main['DATAID'], $selectedMonth);
            $subStmt->execute();
            $subResult = $subStmt->get_result();

            if ($subResult->num_rows > 0) {
                while ($sub = $subResult->fetch_assoc()) {
                    fputcsv($output, [
                        '',
                        '',
                        $sub['SUBNAME'],
                        $sub['SASARAN'],
                        $sub['PENCAPAIAN'],
                        $sub['CATATAN'],
                        $sub['JENISUNIT']
                    ]);
                }
            } else {
                // Write "No Sub-KPI Available" if no sub-KPIs exist
                fputcsv($output, ['', '', 'No Sub-KPI Available', '', '', '', '']);
            }

            $bil++;
        }

        // Add empty row between data types
        fputcsv($output, []);
    }

    fclose($output);
    exit();
}
?>