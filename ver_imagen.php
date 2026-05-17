<?php
require_once 'config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Content-Type: image/jpeg');
    readfile('uploads/default.jpg');
    exit;
}

// Cache por 30 días
$expires = 60 * 60 * 24 * 30;
header('Cache-Control: public, max-age=' . $expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

$stmt = $conn->prepare("SELECT imagen_blob, imagen_tipo FROM plantas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if ($row && !empty($row['imagen_blob'])) {
    $tipo = !empty($row['imagen_tipo']) ? $row['imagen_tipo'] : 'image/jpeg';
    header('Content-Type: ' . $tipo);
    echo $row['imagen_blob'];
} else {
    header('Content-Type: image/jpeg');
    readfile('uploads/default.jpg');
}
?>