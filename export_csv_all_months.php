<?php
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedYear = intval($_POST['year']);

    // Define the months array
    $months = [
        1 => 'JANUARY', 2 => 'FEBRUARY', 3 => 'MARCH', 4 => 'APRIL',
        5 => 'MAY', 6 => 'JUNE', 7 => 'JULY', 8 => 'AUGUST',
        9 => 'SEPTEMBER', 10 => 'OCTOBER', 11 => 'NOVEMBER', 12 => 'DECEMBER'
    ];

    // Define the data types to include
    $dataTypes = ['KENAF', 'PENTADBIRAN', 'TEMBAKAU'];

    // Prepare CSV file
    $filename = "KPI_LKTN_{$selectedYear}_All_Months.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    foreach ($months as $monthNumber => $monthName) {
        // Write the month title
        fputcsv($output, ["KPI LKTN TAHUN {$selectedYear} BULAN {$monthName}"]);
        fputcsv($output, []); // Empty row for spacing

        foreach ($dataTypes as $dataType) {
            // Write the data type as a title
            fputcsv($output, [$dataType]);
            fputcsv($output, ['Bil', 'Main KPI', 'Sub-KPI', 'Target', 'Achievement', 'Notes', 'Unit']);

            // Fetch KPI data
            $sql = "SELECT m.DATAID, m.DATANAME, s.SUBNAME, s.SASARAN, s.PENCAPAIAN, s.CATATAN, s.JENISUNIT
                    FROM kpi2.kpimaindata m
                    LEFT JOIN kpi2.kpisubdata s ON m.DATAID = s.DATAID AND s.DATAMONTH = ?
                    WHERE m.DATATYPE = ? AND YEAR(m.DATADATE) = ?
                    ORDER BY m.DATAID, s.SUBID";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $monthNumber, $dataType, $selectedYear);
            $stmt->execute();
            $result = $stmt->get_result();

            $bil = 1;
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    $bil,
                    $row['DATANAME'],
                    $row['SUBNAME'] ?? 'No Sub-KPI',
                    $row['SASARAN'],
                    $row['PENCAPAIAN'],
                    $row['CATATAN'],
                    $row['JENISUNIT']
                ]);
                $bil++;
            }

            // Add empty row between data types
            fputcsv($output, []);
        }

        // Add empty row between months
        fputcsv($output, []);
    }

    fclose($output);
    exit();
}
?>
