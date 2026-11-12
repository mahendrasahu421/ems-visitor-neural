<?php
include('EvaraFroentend/layout/head.php');
include('EvaraFroentend/layout/header.php');


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
    <section class=" pb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 m-auto">
                    <div class="row">

                        <div class="col-lg-3"></div>
                        <div class="col-lg-6">
                            <div class="login_wrap widget-taber-content p-30 background-white border-radius-5">
                                <div class="padding_eight_all bg-white">
                                    <div class="heading_s1">
                                        <h3 class="mb-30">Add Expenses</h3>
                                    </div>

                                    <form action="add-new-expenses" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="expensesCategories"><strong>Select Expenses Type</strong></label>
                                            <select required class="form-control" name="expense_categories_id" id="expense_categories_id">
                                                <option value="0">---Select Expencese Categories---</option>
                                                <?php foreach ($categories as $categoriesEx) : ?>
                                                    <option value="<?php echo $categoriesEx['id']; ?>">
                                                        <?php echo $categoriesEx['categories']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="categories"><strong>Expenses Name</strong></label>
                                            <input type="text" class="form-control" id="categories" aria-describedby="emailHelp" placeholder="Expenses Name">

                                        </div>
                                        <div class="form-group">
                                            <label for="categories"><strong>Expenses Cost</strong></label>
                                            <input type="text" class="form-control" id="categories" aria-describedby="emailHelp" placeholder="Expenses Cost">

                                        </div>
                                        <div class="form-group">
                                            <label for="categories"><strong>Expenses Date</strong></label>
                                            <input type="date" class="form-control" id="categories" aria-describedby="emailHelp" placeholder="Expenses Cost">

                                        </div>
                                        <div class="form-group">
                                            <label for="categories"><strong>Expenses Mode</strong></label>
                                            <select required class="form-control" name="expense_mode" id="expense_categories_id">
                                                <option value="0">---Select Expenses Mode---</option>
                                                <option value="1">Case</option>
                                                <option value="2">online</option>

                                            </select>

                                        </div>
                                        <div class="form-group">
                                            <label for="categories"><strong>Expenses Invoice</strong></label>
                                            <input type="file" class="form-control" id="categories" aria-describedby="emailHelp" placeholder="Expenses Invoice" name="expense_invoice">

                                        </div>
                                        <div class="form-group">
                                            <label for="expensesCategories"><strong>Expenses Status</strong></label>
                                            <select class="form-control" id="expensesCategories" name="expense_status">
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