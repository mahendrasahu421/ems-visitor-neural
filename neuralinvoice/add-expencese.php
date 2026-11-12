<?php
include("EvaraFroentend/layout/head.php");
include("EvaraFroentend/layout/header.php");

$sqlexpense_categories = "SELECT id, categories FROM expense_categories WHERE status = 1";
$resultsexpense_categories = $con->query($sqlexpense_categories);

// Check if the query was successful
if ($resultsexpense_categories === FALSE) {
    die("Error: " . $con->error);
}

// Fetch client data and store it in an associative array
$expense_categories = array();
while ($expenseCategories = $resultsexpense_categories->fetch_assoc()) {
    $categories[] = $expenseCategories;
}

// Close the result set
$resultsexpense_categories->close();

// Close the database connection
$con->close();
?>
<main class="main">
    <section class="mt-15 pb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 m-auto">
                    <div class="row">

                        <div class="col-lg-3"></div>
                        <div class="col-lg-6">
                            <div class="login_wrap widget-taber-content p-30 background-white border-radius-5">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3 class="mb-0">Add Expense</h3>
                                    <a href="add-expencese-list.php" class="btn btn-sm btn-outline-secondary">Back to List</a>
                                </div>
                                    <div class="heading_s1">
                                    </div>

                                    <form action="addExpenseAction.php" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="expensesCategories"><strong>Select Expenses Type</strong></label>
                                            <select required class="form-control" name="expense_category_id" id="expense_category_id">
                                                <option value="0">---Select Expencese Categories---</option>
                                                <?php foreach ($categories as $categoriesEx) : ?>
                                                    <option value="<?php echo $categoriesEx['id']; ?>">
                                                        <?php echo $categoriesEx['categories']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="categories"><strong>Expenses Name</strong></label>
                                            <input type="text" class="form-control" name="expense_name" id="expense_name" aria-describedby="emailHelp" placeholder="Expenses Name">

                                        </div>
                                        <div class="form-group">
                                            <label for="categories"><strong>Expenses Cost</strong></label>
                                            <input type="text" class="form-control" name="expense_cost" id="expense_cost" aria-describedby="emailHelp" placeholder="Expenses Cost">

                                        </div>
                                        <div class="form-group">
                                            <label for="categories"><strong>Expenses Date</strong></label>
                                            <input type="date" class="form-control" name="expense_date" id="expense_date" aria-describedby="emailHelp" placeholder="Expenses Cost">

                                        </div>
                                        <div class="form-group">
                                            <label for="categories"><strong>Payment Mode</strong></label>
                                            <select required class="form-control" name="expense_mode" id="expense_mode">
                                                <option value="0">---Select Payment Mode---</option>
                                                <option value="1">Case</option>
                                                <option value="2">Online</option>

                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="categories"><strong>Expenses Invoice</strong></label>
                                            <input type="file" class="form-control" name="expense_invoice" id="expense_invoice" aria-describedby="emailHelp" placeholder="Expenses Invoice">

                                        </div>
                                        <div class="form-group">
                                            <label for="expensesCategories"><strong>Expenses Status</strong></label>
                                            <select class="form-control" id="expense_status" name="expense_status">
                                                <option value="0">--Select--Expenses--Status--</option>
                                                <option value="1">Active</option>
                                                <option value="2">Inactive</option>

                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php  include("EvaraFroentend/layout/footer.php"); ?>