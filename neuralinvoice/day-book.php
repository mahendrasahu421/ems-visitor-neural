<?php
include("EvaraFroentend/layout/head.php");
include("EvaraFroentend/layout/header.php");

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Check if from and to dates are provided
$fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : '';
$toDate = isset($_GET['toDate']) ? $_GET['toDate'] : '';

// Ensure that both from and to dates are provided for a valid date range
if (empty($fromDate) && empty($toDate)) {
    $oneMonthAgo = date('Y-m-d', strtotime('-1 month'));
    $fromDate = $oneMonthAgo;
    $toDate = date('Y-m-d'); // Current date
}

// Build the SQL queries based on the provided or default dates
$sqlrecived_payment = "SELECT recived_amount ,taxinvoiceNumber FROM recived_payment ";
$sqlexpenses = "SELECT * FROM expenses";

$resultRecivedPayment = $con->query($sqlrecived_payment);
$resultExpenses = $con->query($sqlexpenses);

if ($resultRecivedPayment === FALSE || $resultExpenses === FALSE) {
    die("Error fetching data from tables: " . $con->error);
}

// Combine data from both tables into a single array
$combinedData = array();

while ($rowRecivedPayment = $resultRecivedPayment->fetch_assoc()) {
    $rowExpenses = $resultExpenses->fetch_assoc(); // Fetch corresponding row from $resultExpenses

    $combinedData[] = array(
        'recived_amount' => $rowRecivedPayment['recived_amount'],
        'taxinvoiceNumber' => $rowRecivedPayment['taxinvoiceNumber'],
        'expense_name' => isset($rowExpenses['expense_name']) ? $rowExpenses['expense_name'] : '',
        'expense_cost' => isset($rowExpenses['expense_cost']) ? $rowExpenses['expense_cost'] : '',
        'expense_date' => isset($rowExpenses['expense_date']) ? $rowExpenses['expense_date'] : '',
        'expense_mode' => isset($rowExpenses['expense_mode']) ? $rowExpenses['expense_mode'] : ''
    );
}

// After the loop, you may need to check if there are remaining rows in $resultExpenses
while ($rowExpenses = $resultExpenses->fetch_assoc()) {
    $combinedData[] = array(
        'recived_amount' => '',
        'taxinvoiceNumber' => '',
        'expense_name' => $rowExpenses['expense_name'],
        'expense_cost' => $rowExpenses['expense_cost'],
        'expense_date' => $rowExpenses['expense_date'],
        'expense_mode' => $rowExpenses['expense_mode']
    );
}


// Close the result sets
$resultRecivedPayment->close();
$resultExpenses->close();

// Close the database connection
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
            <div class="card-header">
                <h3 style="text-align: center;">Day Book
            </div>
        </div>
        <div class="col-md-12">

            <form method="get" class="form-inline mb-5 mt-1 p-3 card-header">
                <div class="row">
                    <div class="col-sm-3">
                        <label for="search" class="mr-2">Search:</label>
                        <input type="text" id="search" class="form-control mr-2" name="search" value="<?php echo $searchQuery; ?>" placeholder="Invoice ID, Client Code, Client Name, OR Invoice Date">


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
        </div>
        <table id="example" class="display mt-5" style="width:100%">
            <thead>
                <tr>
                    <th>Sr.No.</th>
                    <!-- <th>Date</th> -->
                    <th>Invoice No.</th>
                    <th>Total Amount</th>
                    <th>Expense Name</th>
                    <th>Expense Cost</th>
                    <th>Expense Mode</th>
                    <th>Expense Date</th>
                    <th>Total Balance</th>
                    <!-- <th>Total Revenue</th> -->


                    <!-- Add more columns as needed -->
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php $totalRevenue = 0; // Initialize $totalRevenue before the loop 
                ?>
                <?php foreach ($combinedData as $rows) : ?>
                    <?php $totalRevenue += floatval($rows['recived_amount']) - floatval($rows['expense_cost']); ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <!-- <td><?php echo $rows['created_at']; ?></td> -->
                        <td><?php echo $rows['taxinvoiceNumber']; ?></td>
                        <td><?php echo $rows['recived_amount']; ?></td>
                        <td><?php echo $rows['expense_name']; ?></td>
                        <td><?php echo $rows['expense_cost']; ?></td>
                        <td><?php echo $rows['expense_mode']; ?></td>
                        <td><?php echo !empty($rows['expense_date']) ? date('d-m-Y', strtotime($rows['expense_date'])) : '-'; ?></td>
                        <td><?php echo $totalRevenue; ?></td>
                        <!-- Add more columns as needed -->
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