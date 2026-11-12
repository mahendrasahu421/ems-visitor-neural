<?php
include("EvaraFroentend/layout/head.php");

include("EvaraFroentend/layout/header.php");
$sqlFetchData = "SELECT expenses.*, expense_categories.categories
                 FROM expenses
                 LEFT JOIN expense_categories ON expenses.expense_category_id = expense_categories.id";
$resultFetchData = $con->query($sqlFetchData);

if ($resultFetchData === FALSE) {
    die("Error fetching data from expenses table: " . $con->error);
}

$expensesData = array();
while ($row = $resultFetchData->fetch_assoc()) {
    $expensesData[] = $row;
}

$resultFetchData->close();
$con->close();
?>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
    label {
        font-weight: 700;
    }
</style>
<div class="container mt-15">
    <div class="row">
        <div class="col-sm-12">
            <div class="card-header d-flex justify-content-between">
                <h3>Expenses List</h3>
                <div>
                    <a href="add-expencese.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Expense</a>
                    <a href="create-expencese-categories.php" class="btn btn-outline-secondary btn-sm">Add Category</a>
                </div>
            </div>
        </div>
        <div class="col-md-12">



            <form method="get" class="form-inline mb-5 mt-1 p-3 card-header">
                <div class="row">
                    <div class="col-sm-3">
                        <label for="search" class="mr-2">Search:</label>
                        <input type="text" id="search" class="form-control mr-2" name="search" value="#" placeholder="Invoice ID, Client Code, Client Name, OR Invoice Date">


                    </div>
                    <div class="col-sm-3">
                        <label for="fromDate" class="mr-2">From Date:</label>
                        <input type="date" id="fromDate" class="form-control mr-2" value="<?php echo $fromDate; ?>" name="fromDate" placeholder="YYYY-MM-DD">


                    </div>
                    <div class="col-sm-3">
                        <label for="toDate" class="mr-2">To Date:</label>
                        <input type="date" id="toDate" class="form-control mr-2" name="toDate" value="<?php echo $toDate; ?>" placeholder="YYYY-MM-DD">


                    </div>
                    <div class="col-sm-3 mt-4">
                        <button type="submit" class="btn btn-success ">Search</button>
                    </div>
                </div>
            </form>
            <!-- <form action="allDataExport.php" method="post">
                    <div class="col-sm-3 mt-4 mb-2">
                        <button type="submit" name="submit" class="btn btn-success ">Export All Data</button>
                    </div>
                </form> -->



        </div>
        <table id="example" class="display mt-5" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Expense Category ID</th>
                    <th>Expense Name</th>
                    <th>Expense Cost</th>
                    <th>Expense Date</th>
                    <th>Expense Mode</th>
                    <th>Expense Invoice</th>
                    <th>Expense Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expensesData as $expense) : ?>
                    <tr>
                        <td><?php echo $expense['id']; ?></td>
                        <td><?php echo $expense['categories']; ?></td>
                        <td><?php echo $expense['expense_name']; ?></td>
                        <td><?php echo $expense['expense_cost']; ?></td>
                        <td><?php echo $expense['expense_date']; ?></td>
                        <td><?php echo $expense['expense_mode']; ?></td>
                        <td>
                            <?php
                            $invoiceFileName = $expense['expense_invoice'];
                            $invoiceFilePath = "uploads/" . $invoiceFileName; // Update the path accordingly

                            // Check if the invoice file exists before creating the link
                            if (file_exists($invoiceFilePath)) {
                                echo '<a href="' . $invoiceFilePath . '">View Invoice</a>';
                            } else {
                                echo 'Invoice not available';
                            }
                            ?>
                        </td>

                        <td><?php echo $expense['expense_status'] == 1 ? "Active" : "Inactive"; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [
                // 'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                // 'pdfHtml5'
            ]
        });
    })
</script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<?php  include("EvaraFroentend/layout/footer.php"); ?>