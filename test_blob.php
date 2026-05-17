<?php
require_once 'config.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) die("ID no válido");
$stmt = $conn->prepare("SELECT id, nombre_comun, imagen_blob, imagen_tipo FROM plantas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (!$row) die("Planta no encontrada");
echo "ID: $id - Nombre: {$row['nombre_comun']}<br>";
if (!empty($row['imagen_blob'])) {
    echo "✅ Imagen encontrada. Tamaño: " . strlen($row['imagen_blob']) . " bytes. Tipo: " . ($row['imagen_tipo'] ?? 'no definido') . "<br>";
    echo '<img src="data:' . ($row['imagen_tipo'] ?? 'image/jpeg') . ';base64,' . base64_encode($row['imagen_blob']) . '" style="max-width:300px">';
} else {
    echo "❌ No hay imagen BLOB (campo vacío o NULL)";
}
?>