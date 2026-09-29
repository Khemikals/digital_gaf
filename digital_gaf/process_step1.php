<?php
session_start();
include 'db_connect.php';

if (isset($_POST['save_bio'])) {
    // 1. Capture Form Data
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    
    // Residency Data
    $birth_place = $conn->real_escape_string($_POST['birth_place']);
    $birth_region = $_POST['birth_region'];
    $birth_constituency = $conn->real_escape_string($_POST['birth_constituency']);
    $res_place = $conn->real_escape_string($_POST['res_place']);
    $res_region = $_POST['res_region'];
    $res_constituency = $conn->real_escape_string($_POST['res_constituency']);
    
    // Physicals & ID
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $ghana_card = $conn->real_escape_string($_POST['ghana_card']);
    $digital_addr = $conn->real_escape_string($_POST['digital_addr']);

    // 2. Calculate Age Automatically
    $today = new DateTime();
    $birthdate = new DateTime($dob);
    $age = $today->diff($birthdate)->y;

    // 3. Handle Passport Photo Upload
    $target_dir = "uploads/passports/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_ext = pathinfo($_FILES["passport_photo"]["name"], PATHINFO_EXTENSION);
    $photo_name = "PP_" . time() . "." . $file_ext;
    $target_file = $target_dir . $photo_name;
    move_uploaded_file($_FILES["passport_photo"]["tmp_name"], $target_file);

    // 4. Save to Session for the final Step 3 Submission
    $_SESSION['bio_data'] = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'age' => $age,
        'gender' => $gender,
        'height' => $height,
        'weight' => $weight,
        'res_region' => $res_region,
        'ghana_card' => $ghana_card,
        'photo' => $photo_name,
        'birth_place' => $birth_place,
        'res_place' => $res_place
    ];

    // 5. Redirect to Step 2 (Education)
    header("Location: apply_step2.php");
    exit();
}
?>