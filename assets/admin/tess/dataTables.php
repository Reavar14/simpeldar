<?php
$table = 'pesan_darah';
$primaryKey = 'no_permintaan';
 
$columns = array(
    array( 'db' => 'mr','dt' => 0 ),
    array( 'db' => 'nama','dt' => 1 ),
    array( 'db' => 'goldarah','dt' => 2 ),
    array( 'db' => 'jenis_darah', 'dt' => 3 ),
    array( 'db' => 'no_permintaan', 'dt' => 4 ),
);
 
$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db'   => 'darah',
    'host' => 'localhost'
);
require('ajax/ssp.class.php');
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);

?>  