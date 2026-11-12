<?php
include '../../connection.php';

// DB table to use
$table = 'countrymaster';

// Table's primary key
$primaryKey = 'CountryID';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
    array('db' => 'CountryID', 'dt' => 0, 'formatter' => function ($d, $row) {
        static $i = 1;
        return $i++;
    }),
    array('db' => 'CountryName',  'dt' => 1),
    array('db' => 'CountryID',     'dt' => 2, 'formatter' => function ($d, $row) {
        return '<a href="index.php?edit=' . (int)$d . '" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';
    })
);

// Where condition
$where = "DelStatus = 1";

// SQL server connection information
// Since connection.php is included, $con is available.

require('../../includes/ssp.class.php');

$request = $_GET;
$request['start'] = $_GET['start'] ?? 0;
$request['length'] = $_GET['length'] ?? 10;

echo json_encode(
    SSP::simple($request, $con, $table, $primaryKey, $columns, $where)
);