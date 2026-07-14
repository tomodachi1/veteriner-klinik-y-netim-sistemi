<?php
require_once 'db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $db->prepare("DELETE FROM patients WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        header("Location: listele.php?msg=deleted");
        exit;
    }
}

// Bir şeyler yanlış gittiyse doğrudan listeye geri dön
header("Location: listele.php");
exit;
?>
