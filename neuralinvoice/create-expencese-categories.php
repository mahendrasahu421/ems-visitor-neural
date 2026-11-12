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

    <section class="pt-15 pb-15">
        <div class="container-fluid">
            <div class="row">

                <div class="col-lg-10 m-auto">
                   
                        <div class="col-sm-12">
                            <div class="card-header d-flex justify-content-between">
                                <h3>Create Expense Categories</h3>
                                <a href="add-expencese-list.php" class="btn btn-sm btn-outline-secondary">Back to List</a>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="login_wrap widget-taber-content p-30 background-white border-radius-10 mb-md-5 mb-lg-0 mb-sm-5">
                                <div class="padding_eight_all bg-white">

                                    <form action="create-new-expencese-categories.php" method="post" id="basic-form" novalidate="novalidate" class="row g-3" enctype="multipart/form-data">
                                        <div class="col-md-3">
                                            <label>Expencese Categories</label>
                                            <select required class="form-control" name="expense_categories_id" id="expense_categories_id">
                                                <option value="0">---Select Expencese Categories---</option>
                                                <?php foreach ($categories as $categoriesEx) : ?>
                                                    <option value="<?php echo $categoriesEx['id']; ?>">
                                                        <?php echo $categoriesEx['categories']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3" id="newInvoiceTextbox">
                                            <label>Categories</label>
                                            <input type="text" name="categories" class="form-control" id="categories" />

                                        </div>
                                        <div class="col-md-3">
                                            <label>Status</label>
                                            <select required class="form-control" name="status" id="status">
                                                <option value="0">---Select Status---</option>
                                                <option value="1">Active</option>
                                                <option value="2">inactive</option>

                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <button class='btn btn-primary' type="submit" name="submit">Submit</button>
                                        </div>
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