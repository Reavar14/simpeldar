<?php
define('BASEPATH', true);
define('ENVIRONMENT', 'development');
require 'application/config/database.php';
$c = $db['default'];
$m = @new mysqli($c['hostname'], $c['username'], $c['password'], $c['database']);

$res = $m->query("SHOW TRIGGERS WHERE `Table` = 'pesan_darah'");
echo "Triggers on pesan_darah: " . $res->num_rows . "\n";
while ($row = $res->fetch_assoc()) {
    echo "  " . $row['Trigger'] . " (" . $row['Event'] . " " . $row['Timing'] . ")\n";
}
