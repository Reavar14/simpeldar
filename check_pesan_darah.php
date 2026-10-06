<?php
define('BASEPATH', true);
define('ENVIRONMENT', 'development');
require 'application/config/database.php';
$c = $db['default'];
$m = @new mysqli($c['hostname'], $c['username'], $c['password'], $c['database']);

$res = $m->query("DESCRIBE pesan_darah");
echo "Columns in darah.pesan_darah:\n";
while ($row = $res->fetch_assoc()) {
    if (in_array($row['Field'], ['no_permintaan', 'mr', 'nama', 'goldarah', 'id_gol_darah', 'status', 'nm_edit', 'tgl_edit'])) {
        echo "  " . $row['Field'] . " (" . $row['Type'] . ") Null:" . $row['Null'] . " Key:" . $row['Key'] . " Default:" . var_export($row['Default'], true) . "\n";
    }
}
