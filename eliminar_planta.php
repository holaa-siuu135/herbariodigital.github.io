<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') exit(json_encode(['error' => 'Método no permitido']));
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) exit(json_encode(['error' => 'ID inválido']));

// Obtener la ruta de la imagen para borrar el archivo físico
$stmt_img = $conn->prepare("SELECT imagen_path FROM plantas WHERE id = ?");
$stmt_img->bind_param("i", $id);
$stmt_img->execute();
$res_img = $stmt_img->get_result();
$row = $res_img->fetch_assoc();
if ($row && $row['imagen_path'] != 'uploads/default.jpg' && file_exists($row['imagen_path'])) {
    unlink($row['imagen_path']); // Eliminar archivo
}
$stmt_img->close();

$del = $conn->prepare("DELETE FROM plantas WHERE id = ?");
$del->bind_param("i", $id);
if ($del->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error al eliminar']);
}
$del->close();
$conn->close();
?>