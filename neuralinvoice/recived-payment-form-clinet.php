<?php
include("EvaraFroentend/layout/head.php");
include("EvaraFroentend/layout/header.php");

// Check if a search query is provided
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

// Build the SQL query based on the provided or default dates
$sqlFetchData = "SELECT statemaster.StateName,countrymaster.CountryName,invoice.*, recived_payment.*, 
                        GROUP_CONCAT(workamount.description) AS descriptions,
                        GROUP_CONCAT(workamount.amount) AS amounts,
                        recived_payment.taxinvoiceNumber,
                        clientmaster.Country,
                        clientmaster.StateID,
                        clientmaster.pinNo,
                        clientmaster.ClientName 
                FROM recived_payment
                LEFT JOIN workamount ON recived_payment.workAmount_invoice_id = workamount.invoice_id
                LEFT JOIN clientmaster ON recived_payment.ClientID = clientmaster.ClientID
                LEFT JOIN statemaster  ON clientmaster.StateID = statemaster.StateID
                LEFT JOIN countrymaster  ON clientmaster.Country = countrymaster.CountryID
                LEFT JOIN invoice ON recived_payment.workAmount_invoice_id = invoice.id
                WHERE (recived_payment.taxinvoiceNumber LIKE '%$searchQuery%'
                    OR clientmaster.ClientName LIKE '%$searchQuery%'
                    OR clientmaster.ClientCode LIKE '%$searchQuery%'
                    OR recived_payment.created_at LIKE '%$searchQuery%')
                    AND (recived_payment.created_at BETWEEN '$fromDate' AND '$toDate')
                    AND recived_payment.status = 1
                GROUP BY recived_payment.taxinvoiceNumber
                ORDER BY recived_payment.id DESC";

$resultFetchData = $con->query($sqlFetchData);

if ($resultFetchData === FALSE) {
    die("Error fetching data from invoice and workamount tables: " . $con->error);
}

$invoiceData = array();
while ($row = $resultFetchData->fetch_assoc()) {
    $row['descriptions'] = explode(',', $row['descriptions']);
    $row['amounts'] = explode(',', $row['amounts']);
    $row['taxinvoiceNumber'] = $row['taxinvoiceNumber'];
    $invoiceData[] = $row;
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
            <div class="card-header">
                <h3 style="text-align: center;">Tax Invoice List
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
                    <th>Date</th>
                    <th>Party Name</th>
                    <th>Country, City</th>
                    <th>Invoice No.</th>
                    <th>Party GST No.</th>
                    <th>Invoice Amount</th>
                    <th>SGST 9%</th>
                    <th>CGST 9%</th>
                    <th>IGST 18%</th>
                    <th>Bank Charge 3%</th>
                    <th>Total Invoice after Tax</th>
                    <th>PreView Invoice</th>

                    <!-- Add more columns as needed -->
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($invoiceData as $invoice) : ?>
                    <?php
                    $invoice_id = $invoice['workAmount_invoice_id'] ?? $invoice['id'];
                    ?>

                    <tr>
                        <td> <?php echo $i++; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($invoice['created_at'])); ?></td>
                        <td><?php echo $invoice['ClientName']; ?></td>
                        <!-- <td><?php echo $invoice['ClientName']; ?></td> -->
                        <td><?php echo $invoice['CountryName']; ?>,<?php echo $invoice['StateName']; ?></td>
                        <td><?php echo $invoice['taxinvoiceNumber']; ?></td>
                        <td><?php echo $invoice['pinNo']; ?></td>
                        <td><?php echo $invoice['finalAmount']; ?></td>
                        <td><?php echo $invoice['sgstAmount']; ?></td>
                        <td><?php echo $invoice['cgstAmount']; ?></td>
                        <td><?php echo $invoice['igstAmount']; ?></td>
                        <td><?php echo $invoice['bankChargeAmount'] ?? '0.00'; ?></td>
                        <td><?php echo $invoice['totalAmount']; ?></td>


                        <td><span class="badge badge-primary"><a href="view-tax-invoice.php?invoice_id=<?php echo $invoice_id; ?>">Preview Invoice</a></span></td>

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
