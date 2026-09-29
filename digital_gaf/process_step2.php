<?php
session_start();
if (isset($_POST['save_edu'])) {
    
    // Save to Session
    $_SESSION['edu_data'] = [
        'jhs_name' => $_POST['jhs_name'],
        'bece_index' => $_POST['bece_index'],
        'shs_name' => $_POST['shs_name'],
        'wassce_index' => $_POST['wassce_index'],
        'grade_eng' => $_POST['grade_eng'],
        'grade_math' => $_POST['grade_math'],
        'grade_sci' => $_POST['grade_sci'],
        'grade_soc' => $_POST['grade_soc']
    ];

    header("Location: apply_step3.php");
    exit();
}
?>