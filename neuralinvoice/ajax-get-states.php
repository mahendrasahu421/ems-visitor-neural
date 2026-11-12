<?php
include '../../connection.php';

$request = $_GET;

$columns = array(
    0 => 's.StateID',
    1 => 'c.CountryName',
    2 => 's.StateName',
);

$sql = "SELECT s.StateID, s.StateName, c.CountryName 
        FROM statemaster as s 
        JOIN countrymaster as c ON s.CountryID = c.CountryID 
        WHERE s.DelStatus = 1 AND c.DelStatus = 1";

$query = $con->query($sql);
$totalData = $query->num_rows;
$totalFiltered = $totalData;

if (!empty($request['search']['value'])) {
    $sql .= " AND ( s.StateName LIKE '%" . $request['search']['value'] . "%' ";
    $sql .= " OR c.CountryName LIKE '%" . $request['search']['value'] . "%' )";
}

$query = $con->query($sql);
$totalFiltered = $query->num_rows;

if (isset($request['order']) && count($request['order'])) {
    $sql .= " ORDER BY " . $columns[$request['order'][0]['column']] . "   " . $request['order'][0]['dir'] . "  ";
} else {
    $sql .= " ORDER BY c.CountryName ASC, s.StateName ASC ";
}

if (isset($request['start']) || isset($request['length'])) {
    $sql .= " LIMIT " . ($request['start'] ?? 0) . " ," . ($request['length'] ?? 10);
}

$query = $con->query($sql);

$data = array();
$i = ($request['start'] ?? 0) + 1;
while ($row = $query->fetch_assoc()) {
    $subdata = array();
    $subdata[] = $i++;
    $subdata[] = $row['CountryName'];
    $subdata[] = $row['StateName'];
    $subdata[] = '<a href="index.php?edit=' . (int)$row['StateID'] . '" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';
    $data[] = $subdata;
}

$json_data = array(
    "draw" => intval($request['draw'] ?? 0),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
);

echo json_encode($json_data);
?>