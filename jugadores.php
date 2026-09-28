<?php
require_once __DIR__ . '/includes/init.php';
requiere_login();
$titulo = 'Jugadores';
$menu = 'jugadores';

// Filtros
$b         = trim($_GET['b'] ?? '');
$categoria = $_GET['categoria'] ?? '';
$estado    = $_GET['estado'] ?? 'activo';

$where = [];
$params = [];
if ($b !== '') {
    $where[] = "(CONCAT(j.nombres,' ',j.apellidos) LIKE ? OR CONCAT(j.apellidos,' ',j.nombres) LIKE ? OR j.cedula LIKE ? OR r.cedula LIKE ?)";
    array_push($params, "%$b%", "%$b%", "%$b%", "%$b%");
}
if ($categoria === 'sin') {
    $where[] = 'j.categoria_id IS NULL';
} elseif ($categoria !== '') {
    $where[] = 'j.categoria_id = ?';
    $params[] = (int) $categoria;
}
if (in_array($estado, ['activo', 'inactivo', 'retirado'], true)) {
    $where[] = 'j.estado = ?';
    $params[] = $estado;
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$base = "FROM jugadores j
         LEFT JOIN categorias c ON c.id = j.categoria_id
         LEFT JOIN representantes r ON r.id = j.representante_id
         $sqlWhere";

// Exportar a Excel (CSV con separador ; y BOM para que Excel respete los acentos)
if (isset($_GET['exportar'])) {
    $filas = q("SELECT j.cedula, j.apellidos, j.nombres, j.fecha_nacimiento, j.sexo, c.nombre categoria, j.posicion,
                       j.tipo_sangre, j.estado, j.fecha_inscripcion,
                       CONCAT(r.nombres,' ',r.apellidos) representante, r.cedula ced_rep, r.telefono
                $base ORDER BY c.edad_min, j.apellidos, j.nombres", $params)->fetchAll();
    bitacora('Exportar', 'Listado de jugadores (' . count($filas) . ')');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="jugadores_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Cédula', 'Apellidos', 'Nombres', 'Fecha nac.', 'Edad', 'Sexo', 'Categoría', 'Posición',
                   'Tipo sangre', 'Estado', 'Inscripción', 'Representante', 'C.I. representante', 'Teléfono'], ';');
    foreach ($filas as $f) {
        fputcsv($out, [$f['cedula'], $f['apellidos'], $f['nombres'], fecha_ve($f['fecha_nacimiento']),
                       edad($f['fecha_nacimiento']), $f['sexo'], $f['categoria'], $f['posicion'], $f['tipo_sangre'],
                       $f['estado'], fecha_ve($f['fecha_inscripcion']), $f['representante'], $f['ced_rep'], $f['telefono']], ';');
    }
    exit;
}

// Paginación
$porPagina = 25;
$total = (int) q("SELECT COUNT(*) $base", $params)->fetchColumn();
$paginas = max(1, (int) ceil($total / $porPagina));
$p = min($paginas, max(1, (int) ($_GET['p'] ?? 1)));
$offset = ($p - 1) * $porPagina;

$jugadores = q("SELECT j.*, c.nombre categoria, CONCAT(r.nombres,' ',r.apellidos) representante, r.telefono tel_rep
                $base ORDER BY j.apellidos, j.nombres LIMIT $porPagina OFFSET $offset", $params)->fetchAll();
$categorias = q('SELECT id, nombre FROM categorias ORDER BY edad_min')->fetchAll();

function url_filtros(array $extra = []): string
{
    $q = array_merge(array_intersect_key($_GET, array_flip(['b', 'categoria', 'estado'])), $extra);
    return 'jugadores.php?' . http_build_query($q);
}

require __DIR__ . '/includes/header.php';
?>
<form class="filtros" method="get">
  <input type="search" name="b" value="<?= e($b) ?>" placeholder="Buscar por nombre o cédula (jugador o representante)">
  <select name="categoria">
    <option value="">Todas las categorías</option>
    <?php foreach ($categorias as $c): ?>
      <option value="<?= $c['id'] ?>" <?= (string) $categoria === (string) $c['id'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
    <?php endforeach; ?>
    <option value="sin" <?= $categoria === 'sin' ? 'selected' : '' ?>>Sin categoría</option>
  </select>
  <select name="estado">
    <?php foreach (['activo' => 'Activos', 'inactivo' => 'Inactivos', 'retirado' => 'Retirados', 'todos' => 'Todos'] as $v => $t): ?>
      <option value="<?= $v ?>" <?= $estado === $v ? 'selected' : '' ?>><?= $t ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn">Filtrar</button>
  <a class="btn btn-sec" href="<?= e(url_filtros(['exportar' => 1])) ?>">Exportar a Excel</a>
  <?php if (puede_editar()): ?><a class="btn btn-primario" href="jugador_form.php">+ Nuevo jugador</a><?php endif; ?>
</form>

<p class="nota"><?= $total ?> jugador(es) encontrado(s).</p>

<div class="tabla-envoltura">
<table class="tabla">
  <thead>
    <tr><th></th><th>Jugador</th><th>Cédula</th><th>Edad</th><th>Categoría</th><th>Posición</th><th>Representante</th><th>Estado</th><th></th></tr>
  </thead>
  <tbody>
  <?php if (!$jugadores): ?>
    <tr><td colspan="9" class="vacio">No hay jugadores con esos criterios.</td></tr>
  <?php endif; ?>
  <?php foreach ($jugadores as $j): $foto = foto_url($j['foto']); ?>
    <tr>
      <td><?php if ($foto): ?><img class="avatar" src="<?= e($foto) ?>" alt=""><?php else: ?><span class="avatar"><?= e(iniciales($j['nombres'], $j['apellidos'])) ?></span><?php endif; ?></td>
      <td><a href="jugador_ver.php?id=<?= $j['id'] ?>"><strong><?= e($j['apellidos']) ?></strong>, <?= e($j['nombres']) ?></a></td>
      <td><?= e($j['cedula'] ?? '—') ?></td>
      <td><?= edad($j['fecha_nacimiento']) ?></td>
      <td><?= e($j['categoria'] ?? '—') ?></td>
      <td><?= e($j['posicion']) ?></td>
      <td><?= e($j['representante'] ?? '—') ?><br><small><?= e($j['tel_rep'] ?? '') ?></small></td>
      <td><span class="etiqueta etiqueta-<?= e($j['estado']) ?>"><?= e(ucfirst($j['estado'])) ?></span></td>
      <td class="acciones">
        <a href="jugador_ver.php?id=<?= $j['id'] ?>">Ver</a>
        <?php if (puede_editar()): ?><a href="jugador_form.php?id=<?= $j['id'] ?>">Editar</a><?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>

<?php if ($paginas > 1): ?>
<nav class="paginacion">
  <?php for ($i = 1; $i <= $paginas; $i++): ?>
    <a class="<?= $i === $p ? 'activo' : '' ?>" href="<?= e(url_filtros(['p' => $i])) ?>"><?= $i ?></a>
  <?php endfor; ?>
</nav>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
