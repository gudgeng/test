<?php
require_once 'tcpdf/tcpdf.php'; // Include the TCPDF library
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

    // Create a new PDF document
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('KPI System');
    $pdf->SetTitle("KPI LKTN {$selectedYear} {$monthName}");
    $pdf->SetHeaderData('', 0, "KPI LKTN TAHUN {$selectedYear} BULAN {$monthName}", '');

    // Set margins
    $pdf->SetMargins(10, 27, 10);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 10);

    // Write the title
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Write(0, "KPI LKTN TAHUN {$selectedYear} BULAN {$monthName}", '', 0, 'C', true, 0, false, false, 0);
    $pdf->Ln(5);

    foreach ($dataTypes as $dataType) {
        // Write the data type title
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Write(0, strtoupper($dataType), '', 0, 'L', true, 0, false, false, 0);
        $pdf->Ln(2);

        // Fetch KPI data
        $sql = "SELECT m.DATAID, m.DATANAME, s.SUBNAME, s.SASARAN, s.PENCAPAIAN, s.CATATAN, s.JENISUNIT
                FROM kpi2.kpimaindata m
                LEFT JOIN kpi2.kpisubdata s ON m.DATAID = s.DATAID AND s.DATAMONTH = ?
                WHERE m.DATATYPE = ? AND YEAR(m.DATADATE) = ?
                ORDER BY m.DATAID, s.SUBID";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $selectedMonth, $dataType, $selectedYear);
        $stmt->execute();
        $result = $stmt->get_result();

        $groupedData = [];
        while ($row = $result->fetch_assoc()) {
            $dataId = $row['DATAID'];
            if (!isset($groupedData[$dataId])) {
                $groupedData[$dataId] = [
                    'DATANAME' => $row['DATANAME'],
                    'SUBITEMS' => []
                ];
            }
            if (!empty($row['SUBNAME'])) {
                $groupedData[$dataId]['SUBITEMS'][] = $row;
            }
        }

        if (!empty($groupedData)) {
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(10, 9, 'Bil', 1, 0, 'C');
            $pdf->Cell(70, 9, 'KPI TAHUN ' . $selectedYear, 1, 0, 'L');
            $pdf->Cell(20, 9, 'Sasaran', 1, 0, 'C');
            $pdf->MultiCell(25, 7, "Pencapaian\nSemasa", 1,'C',0,0);
            $pdf->Cell(40, 9, 'Catatan', 1, 0, 'L');
            $pdf->Cell(30, 9, 'Bahagian / Unit', 1, 1, 'C');

            $bil = 1;
            foreach ($groupedData as $dataId => $data) {
                // Main KPI row
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(10, 7, $bil, 1, 0, 'C');
                $pdf->Cell(70, 7, $data['DATANAME'], 1, 0, 'L');
                $pdf->Cell(20, 7, '', 1, 0, 'C'); // Empty Target cell
                $pdf->Cell(25, 7, '', 1, 0, 'C'); // Empty Achievement cell
                $pdf->Cell(40, 7, '', 1, 0, 'L'); // Empty Notes cell
                $pdf->Cell(30, 7, '', 1, 1, 'C'); // Empty Unit cell

                // Sub-KPI rows
                if (!empty($data['SUBITEMS'])) {
                    $subBil = 'a';
                    foreach ($data['SUBITEMS'] as $sub) {
                        $pdf->SetFont('helvetica', '', 10);

                        // Calculate the height of the "Catatan" cell
                        $catatanHeight = $pdf->getStringHeight(40, $sub['CATATAN']); // 40 is the width of the "Catatan" cell
                        $rowHeight = max(7, $catatanHeight); // Ensure a minimum height of 7

                        // Render the first few cells with the calculated row height
                        $pdf->Cell(10, $rowHeight, '', 1, 0, 'C'); // Empty Bil cell for sub-items
                        $pdf->Cell(70, $rowHeight, $sub['SUBNAME'], 1, 0, 'L');
                        $pdf->Cell(20, $rowHeight, $sub['SASARAN'], 1, 0, 'C');
                        $pdf->Cell(25, $rowHeight, $sub['PENCAPAIAN'], 1, 0, 'C');

                        // Render the "Catatan" cell with MultiCell
                        $x = $pdf->GetX(); // Get current X position
                        $y = $pdf->GetY(); // Get current Y position
                        $pdf->MultiCell(40, $rowHeight, $sub['CATATAN'], 1, 'L', 0);

                        // Adjust the cursor position to prevent overlapping
                        $pdf->SetXY($x + 40, $y);

                        // Render the remaining cell
                        $pdf->Cell(30, $rowHeight, $sub['JENISUNIT'], 1, 1, 'C'); // Unit cell
                        $subBil++;
                    }
                }
                $bil++;
            }
        } else {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Write(0, 'No data available.', '', 0, 'L', true, 0, false, false, 0);
        }

        $pdf->Ln(5); // Add spacing between data types
    }

    // Output the PDF
    $pdf->Output("KPI_LKTN_{$selectedYear}_{$monthName}.pdf", 'I');
    exit();
}
?>