<?php
include '../connection.php';

// DB table to use
$table = 'companymaster';

// Table's primary key
$primaryKey = 'CompanyID';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
    array('db' => 'CompanyID', 'dt' => 0, 'formatter' => function ($d, $row) {
        // This is a placeholder for the row number, which is handled by DataTables on the client-side
        // Or can be handled by a counter in the loop if needed. For now, we return the ID.
        return $d;
    }),
    array('db' => 'CompanyName',  'dt' => 1),
    array('db' => 'CompanyCode',   'dt' => 2),
    array('db' => 'EmailAddress',  'dt' => 3),
    array('db' => 'MobileNo',      'dt' => 4),
    array('db' => 'GSTNumber',     'dt' => 5),
    array('db' => 'DelStatus',     'dt' => 6, 'formatter' => function ($d, $row) {
        if ((int)$d === 1) {
            return '<span class="badge bg-success">Active</span>';
        } else {
            return '<span class="badge bg-secondary">Inactive</span>';
        }
    }),
    array('db' => 'CompanyID',     'dt' => 7, 'formatter' => function ($d, $row) {
        $delStatus = (int)$row['DelStatus'];
        $editBtn = '<a href="index.php?edit=' . (int)$d . '#companyForm"
                        class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

        if ($delStatus === 1) {
            $statusBtn = '<a href="index.php?delete=' . (int)$d . '"
                            class="btn btn-sm btn-outline-danger"
                            onclick="return confirm(\'Deactivate this company?\');" data-bs-toggle="tooltip" data-bs-placement="top" title="Deactivate"><i class="fas fa-times-circle"></i></a>';
        } else {
            $statusBtn = '<a href="index.php?activate=' . (int)$d . '"
                            class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Activate"><i class="fas fa-check-circle"></i></a>';
        }
        return $editBtn . ' ' . $statusBtn;
    })
);

// SQL server connection information
// Since connection.php is included, $con is available.

require('../includes/ssp.class.php');

echo json_encode(
    SSP::simple($_GET, $con, $table, $primaryKey, $columns)
);

```

#### 4. `includes/ssp.class.php` (New Helper File)

DataTables ke server-side processing ko aasan banane ke liye, maine ek standard helper class `ssp.class.php` add ki hai.

```diff
--- /dev/null
+++ b/c/xampp/htdocs/neuralinvoice/includes/ssp.class.php
@@ -0,0 +1,374 @@
+<?php
+
+/*
+ * Helper functions for building a DataTables server-side processing SQL query.
+ *
+ * The static functions in this class are just helper functions to help build
+ * the SQL used in the DataTables sourcing. These functions are not intended to
+ * be used outside of this file.
+ */
+class SSP
+{
+    /**
+     * Create the data output array for the DataTables rows
+     *
+     *  @param  array $columns Column information array
+     *  @param  array $data    Data from the SQL get
+     *  @return array          Formatted data in a row based format
+     */
+    static function data_output($columns, $data)
+    {
+        $out = array();
+
+        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
+            $row = array();
+
+            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
+                $column = $columns[$j];
+
+                // Is there a formatter?
+                if (isset($column['formatter'])) {
+                    $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
+                } else {
+                    $row[$column['dt']] = $data[$i][$columns[$j]['db']];
+                }
+            }
+
+            $out[] = $row;
+        }
+
+        return $out;
+    }
+
+
+    /**
+     * Paging
+     *
+     * Construct the LIMIT clause for server-side processing SQL query
+     *
+     *  @param  array $request Data sent to server by DataTables
+     *  @param  array $columns Column information array
+     *  @return string SQL limit clause
+     */
+    static function limit($request, $columns)
+    {
+        $limit = '';
+
+        if (isset($request['start']) && $request['length'] != -1) {
+            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
+        }
+
+        return $limit;
+    }
+
+
+    /**
+     * Ordering
+     *
+     * Construct the ORDER BY clause for server-side processing SQL query
+     *
+     *  @param  array $request Data sent to server by DataTables
+     *  @param  array $columns Column information array
+     *  @return string SQL order by clause
+     */
+    static function order($request, $columns)
+    {
+        $order = '';
+
+        if (isset($request['order']) && count($request['order'])) {
+            $orderBy = array();
+            $dtColumns = self::pluck($columns, 'dt');
+
+            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
+                // Convert the column index into the column data property
+                $columnIdx = intval($request['order'][$i]['column']);
+                $requestColumn = $request['columns'][$columnIdx];
+
+                $columnIdx = array_search($requestColumn['data'], $dtColumns);
+                $column = $columns[$columnIdx];
+
+                if ($requestColumn['orderable'] == 'true') {
+                    $dir = $request['order'][$i]['dir'] === 'asc' ?
+                        'ASC' :
+                        'DESC';
+
+                    $orderBy[] = '`' . $column['db'] . '` ' . $dir;
+                }
+            }
+
+            if (count($orderBy)) {
+                $order = 'ORDER BY ' . implode(', ', $orderBy);
+            }
+        }
+
+        return $order;
+    }
+
+
+    /**
+     * Searching / Filtering
+     *
+     * Construct the WHERE clause for server-side processing SQL query.
+     *
+     * NOTE this does not match the built-in DataTables filtering which does it
+     * word by word on any field. It's possible to do here performance on large
+     * databases would be very poor
+     *
+     *  @param  array $request Data sent to server by DataTables
+     *  @param  array $columns Column information array
+     *  @param  mysqli $db      Database connection
+     *  @return string SQL where clause
+     */
+    static function filter($request, $columns, $db)
+    {
+        $globalSearch = array();
+        $columnSearch = array();
+        $dtColumns = self::pluck($columns, 'dt');
+
+        if (isset($request['search']) && $request['search']['value'] != '') {
+            $str = $request['search']['value'];
+
+            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
+                $requestColumn = $request['columns'][$i];
+                $columnIdx = array_search($requestColumn['data'], $dtColumns);
+                $column = $columns[$columnIdx];
+
+                if ($requestColumn['searchable'] == 'true') {
+                    $globalSearch[] = "`" . $column['db'] . "` LIKE " . self::quote($db, "%" . $str . "%");
+                }
+            }
+        }
+
+        // Individual column filtering
+        if (isset($request['columns'])) {
+            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
+                $requestColumn = $request['columns'][$i];
+                $columnIdx = array_search($requestColumn['data'], $dtColumns);
+                $column = $columns[$columnIdx];
+
+                $str = $requestColumn['search']['value'];
+
+                if (
+                    $requestColumn['searchable'] == 'true' &&
+                    $str != ''
+                ) {
+                    $columnSearch[] = "`" . $column['db'] . "` LIKE " . self.quote($db, "%" . $str . "%");
+                }
+            }
+        }
+
+        // Combine the filters into a single string
+        $where = '';
+
+        if (count($globalSearch)) {
+            $where = '(' . implode(' OR ', $globalSearch) . ')';
+        }
+
+        if (count($columnSearch)) {
+            $where = $where === '' ?
+                implode(' AND ', $columnSearch) :
+                $where . ' AND ' . implode(' AND ', $columnSearch);
+        }
+
+        if ($where !== '') {
+            $where = 'WHERE ' . $where;
+        }
+
+        return $where;
+    }
+
+    /**
+     * Perform the SQL queries needed for an server-side processing requested,
+     * utilising the helper functions of this class, limit(), order() and
+     * filter() among others. The returned array is ready to be encoded as JSON
+     * in response to an SSP request, or can be modified if needed.
+     *
+     *  @param  array $request Data sent to server by DataTables
+     *  @param  mysqli $db      Database connection
+     *  @param  string $table   SQL table to query
+     *  @param  string $primaryKey Primary key of the table
+     *  @param  array $columns Column information array
+     *  @return array          Server-side processing response array
+     */
+    static function simple($request, $db, $table, $primaryKey, $columns)
+    {
+        // Build the SQL query string from the request
+        $limit = self::limit($request, $columns);
+        $order = self::order($request, $columns);
+        $where = self::filter($request, $columns, $db);
+
+        // Main query to actually get the data
+        $data_sql = "SELECT `" . implode("`, `", self::pluck($columns, 'db')) . "`
+			 FROM `$table`
+			 $where
+			 $order
+			 $limit";
+        $data = self::sql_exec($db, $data_sql);
+
+        // Data set length after filtering
+        $resFilterLength = self::sql_exec($db, "SELECT COUNT(`{$primaryKey}`)
+			 FROM   `$table`
+			 $where");
+        $recordsFiltered = $resFilterLength[0][0];
+
+        // Total data set length
+        $resTotalLength = self::sql_exec($db, "SELECT COUNT(`{$primaryKey}`)
+			 FROM   `$table`");
+        $recordsTotal = $resTotalLength[0][0];
+
+
+        /*
+         * Output
+         */
+        return array(
+            "draw"            => isset($request['draw']) ?
+                intval($request['draw']) :
+                0,
+            "recordsTotal"    => intval($recordsTotal),
+            "recordsFiltered" => intval($recordsFiltered),
+            "data"            => self::data_output($columns, $data)
+        );
+    }
+
+    /**
+     * Execute an SQL query on the database
+     *
+     * @param  mysqli $db  Database connection
+     * @param  string $sql SQL query to execute.
+     * @return array         Result from the query
+     */
+    static function sql_exec($db, $sql)
+    {
+        $result = $db->query($sql);
+        if ($result === false) {
+            self::fatal("SQL Error: " . $db->error);
+        }
+
+        $data = [];
+        while ($row = $result->fetch_assoc()) {
+            $data[] = array_values($row);
+        }
+
+        // For COUNT queries
+        if (empty($data) && strpos(strtoupper($sql), 'COUNT') !== false) {
+            $result->data_seek(0);
+            $row = $result->fetch_row();
+            return [$row];
+        }
+
+        // For regular queries
+        $result->data_seek(0);
+        $data_assoc = [];
+        while ($row = $result->fetch_assoc()) {
+            $data_assoc[] = $row;
+        }
+
+        return $data_assoc;
+    }
+
+
+    /**
+     * Throw a fatal error.
+     *
+     * This writes out an error message in a JSON string which DataTables will
+     * see and show to the user in the browser.
+     *
+     * @param  string $msg Message to send to the client
+     */
+    static function fatal($msg)
+    {
+        echo json_encode(array(
+            "error" => $msg
+        ));
+
+        exit(0);
+    }
+
+    /**
+     * Pull a particular property from each assoc. array in a numeric array, 
+     * returning a numeric array of the pulled values.
+     *
+     *  @param  array  $a      Array to get data from
+     *  @param  string $prop   Property to read
+     *  @return array          Array of property values
+     */
+    static function pluck($a, $prop)
+    {
+        $out = array();
+
+        for ($i = 0, $len = count($a); $i < $len; $i++) {
+            $out[] = $a[$i][$prop];
+        }
+
+        return $out;
+    }
+
+    /**
+     * Quote a string for use in an SQL statement
+     *
+     * @param mysqli $db Database connection
+     * @param string $str String to be escaped
+     * @return string Escaped string
+     */
+    static function quote($db, $str)
+    {
+        return "'" . $db->real_escape_string($str) . "'";
+    }
}
```

