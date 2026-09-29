<?php
include 'db_connect.php';

echo "<h2>Running GAF Screening Protocols...</h2>";

// ---------------------------------------------------
// LOGIC 1: THE KNOCKOUT (Age & Height)
// ---------------------------------------------------

$sql_knockout = "
    UPDATE applications a
    JOIN applicants p ON a.applicant_id = p.applicant_id
    SET a.application_status = 'Rejected', 
        a.remarks = 'Failed Basic Physical/Age Requirement'
    WHERE 
        -- Rule 1: Age Check (Must be 18-26)
        (DATEDIFF(NOW(), p.date_of_birth) / 365.25 < 18 
         OR DATEDIFF(NOW(), p.date_of_birth) / 365.25 > 26)
        OR 
        -- Rule 2: Height Check (cm)
        (p.gender = 'Male' AND p.height_cm < 168)
        OR 
        (p.gender = 'Female' AND p.height_cm < 160)
";

if ($conn->query($sql_knockout) === TRUE) {
    echo "✅ Knockout Protocol Complete: Under-aged and short applicants rejected.<br>";
} else {
    echo "❌ Error in Knockout Protocol: " . $conn->error . "<br>";
}

// ---------------------------------------------------
// LOGIC 2: REGIONAL VENUE ASSIGNMENT
// ---------------------------------------------------

$sql_venue = "
    UPDATE applications a
    JOIN applicants p ON a.applicant_id = p.applicant_id
    SET a.assigned_venue = CASE 
        WHEN p.region_of_residence IN ('Greater Accra', 'Central', 'Western', 'Western North') THEN 'El-Wak Stadium, Accra'
        WHEN p.region_of_residence IN ('Ashanti', 'Bono', 'Ahafo', 'Bono East', 'Eastern') THEN '4BN, Kumasi'
        WHEN p.region_of_residence IN ('Northern', 'Savannah', 'North East', 'Upper East', 'Upper West', 'Oti', 'Volta') THEN '6BN, Tamale'
        ELSE 'General HQ'
    END
    WHERE a.application_status = 'Submitted' 
";

if ($conn->query($sql_venue) === TRUE) {
    echo "✅ Venue Assignment Complete: Candidates sorted by Region.<br>";
} else {
    echo "❌ Error assigning venues: " . $conn->error;
}
?>