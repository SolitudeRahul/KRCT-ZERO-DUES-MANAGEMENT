<?php

// Include the number to words function
include('number_to_words.php');

// Set the time zone to Asia/Kolkata
date_default_timezone_set('Asia/Kolkata');

require('fpdf186/fpdf.php');

// Retrieve data from the database
$servername = "localhost";
$username = "root";
$password = "root";
$database = "krctnodues";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the username from the session
session_start();
if (!isset($_SESSION["username"])) {
    // If not logged in, redirect to login page
    header("Location: index.html");
    exit;
}
$username = $_SESSION["username"];

// SQL query to retrieve student details based on username
$sql = "SELECT s.name, s.department, s.tutionfee, s.hostelfee, s.busfee, s.miscellaneousfee,s.messfee,s.examfee,s.libraryfee,s.fine
        FROM login1 l
        JOIN student1 s ON l.name = s.name
        WHERE l.username = '$username'";
$result = $conn->query($sql);

// Initialize variables for student details
$name = '';
$department = '';
$tutionfee = '';
$hostelfee = '';
$busfee = '';
$miscellaneousfee = '';
$mess_fee="";
$exam_fee="";
$library_fee="";
$fine="";

if ($result->num_rows > 0) {
    // Fetch student details
    $row = $result->fetch_assoc();
    $name = $row["name"];
    $department = $row["department"];
    $tutionfee = $row["tutionfee"];
    $hostelfee = $row["hostelfee"];
    $busfee = $row["busfee"];
    $miscellaneousfee = $row["miscellaneousfee"];
    $mess_fee=$row["messfee"];
    $exam_fee=$row["examfee"];
    $library_fee=$row["libraryfee"];
    $fine=$row["fine"];

    // Calculate total amount
    $total_amount = $tutionfee + $hostelfee + $busfee + $miscellaneousfee+$mess_fee+$exam_fee+$library_fee+$fine;

    // Convert total amount to words
    $total_amount_in_words = numberToWords($total_amount);

} else {
    // No data found in the database
    $error_message = "No data found in the database for the logged-in user.";
}

// Close database connection
$conn->close();

if (!isset($error_message)) {
    // Create new PDF document
    class PDF extends FPDF {
        function Header() {
            // Add the date and time to the header
           // $this->SetFont('Arial', 'I', 8);
           // $this->Cell(0, 10, date('Y-m-d H:i:s A'), 0, 1, 'R');
             // Add the date and time to the header
             $this->SetFont('Arial', 'I', 8);
             $this->Cell(0, 10, date('d-m-Y h:i:s A'), 0, 1, 'R');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('Arial', 'B', 16);

    // Title
    $pdf->Cell(0, 10, 'Zero Dues', 0, 1, 'C');

    // Line break
    $pdf->Ln(10);

    // Set font
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(70, 10, 'Name', 1);
    $pdf->Cell(70, 10, $name, 1);
    $pdf->Ln();

    $pdf->Cell(70, 10, 'Department', 1);
    $pdf->Cell(70, 10, $department, 1);
    $pdf->Ln();


    // Table rows
    $pdf->Cell(70, 10, 'Tuition Fee', 1);
    $pdf->Cell(70, 10, $tutionfee, 1);
    $pdf->Ln();

    $pdf->Cell(70, 10, 'Hostel Fee', 1);
    $pdf->Cell(70, 10, $hostelfee, 1);
    $pdf->Ln();

    $pdf->Cell(70, 10, 'Bus Fee', 1);
    $pdf->Cell(70, 10, $busfee, 1);
    $pdf->Ln();

    $pdf->Cell(70, 10, 'Miscellaneous Fee', 1);
    $pdf->Cell(70, 10, $miscellaneousfee, 1);
    $pdf->Ln();

    $pdf->Cell(70, 10, 'Mess Fee', 1);
    $pdf->Cell(70, 10, $mess_fee, 1);
    $pdf->Ln();

    $pdf->Cell(70, 10, 'Exam Fee', 1);
    $pdf->Cell(70, 10, $exam_fee, 1);
    $pdf->Ln();

    $pdf->Cell(70, 10, 'Library Fee', 1);
    $pdf->Cell(70, 10, $library_fee, 1);
    $pdf->Ln();

    $pdf->Cell(70, 10, 'Fine', 1);
    $pdf->Cell(70, 10, $fine, 1);
    $pdf->Ln();

    // Total amount row
    $pdf->Cell(70, 10, 'Total', 1, 0, 'L');
    $pdf->Cell(70, 10, $total_amount, 1, 1);

    $pdf->Ln();
    // Total amount in words
    $pdf->Cell(0, 10, 'Total Amount in Words: ' . $total_amount_in_words, 0, 1, 'L');

    // Line break
    $pdf->Ln(15);
    // Student signature
    $pdf->Cell(0, 10, 'Student Signature', 0, 1, 'L');

    // Staff Incharge sign
    $pdf->Cell(0, 10, 'Staff Signature', 0, 1, 'R');

    // Output PDF
    $pdf->Output('no_dues_form '.$name.'.pdf', 'D');
} else {
    echo $error_message;
}
?>
