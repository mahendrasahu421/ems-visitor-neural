<?php
include("EvaraFroentend/layout/head.php");
include("EvaraFroentend/layout/header.php");

// Fetch client records from the database
$sqlCountry = "SELECT * FROM countrymaster";
$resultCountry = $con->query($sqlCountry);

// Check if the query was successful
if ($resultCountry === FALSE) {
    die("Error: " . $con->error);
}

// Fetch client data and store it in an associative array
$country = array();
while ($rowCountry = $resultCountry->fetch_assoc()) {
    $country[] = $rowCountry;
}

// Close the result set
$resultCountry->close();

// Close the database conection
$con->close();
?>

<style>
    label {
        font-weight: 700;
    }
</style>

<?php
// Show server-side validation errors or success messages (from redirect query string)
$errorMsg = isset($_GET['error']) ? (string) $_GET['error'] : '';
$successMsg = isset($_GET['success']) ? (string) $_GET['success'] : '';
?>

<div class="container mt-15">
    <!-- Display Error Message -->
    <?php if ($errorMsg !== ''): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display Success Message -->
    <?php if ($successMsg !== ''): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="client.php" class="">Client List</a>
                    <h3 class="card-title" style="text-align:center;">Client Information</h3>
                </div>

                <div class="card-body">
                    <form action="create-client.php" method="post" id="basic-form" novalidate="novalidate"
                        class="row g-3" enctype="multipart/form-data">


                        <!-- Client Name -->
                        <div class="col-md-4">
                            <label for="client_name" class="form-label">Client Name</label>
                            <input required type="text" name="client_name" id="client_name" class="form-control"
                                label="client_name" placeholder="Client Name" value="" />
                        </div>

                        <!-- Concern Person -->
                        <div class="col-md-4">
                            <label for="concern_person" class="form-label">Concern Person</label>
                            <input required type="text" name="concern_person" id="concern_person" class="form-control"
                                label="concern_person" placeholder="Concern Person" value="" />
                        </div>

                        <!-- CIN -->
                        <div class="col-md-4">
                            <label for="cin" class="form-label">CIN <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="cin" id="cin" placeholder="CIN" class="form-control" label="cin" />
                        </div>

                        <!-- Client Logo - Optional Field -->
                        <div class="col-md-4">
                            <label for="client_logo" class="form-label">Client Logo <span
                                    class="text-muted">(Optional)</span></label>
                            <input type="file" name="client_logo" id="client_logo" class="form-control"
                                accept="image/png,image/jpeg,image/jpg,image/webp">
                            <small class="form-text text-muted">Allowed formats: PNG, JPEG, JPG, WEBP.</small>
                        </div>

                        <!-- Website -->
                        <div class="col-md-4">
                            <label for="website" class="form-label">Website</label>
                            <input required type="text" name="website" id="website" placeholder="Website"
                                class="form-control" label="website" />
                        </div>

                        <!-- Email -->
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input required type="email" name="email" id="email" placeholder="Email"
                                class="form-control" label="email" />
                        </div>

                        <!-- mobile -->
                        <div class="col-md-4">
                            <label for="mobile" class="form-label">Mobile</label>
                            <input required type="mobile" name="mobile" id="mobile" placeholder="Mobile"
                                class="form-control" label="mobile" />
                        </div>

                        <!-- Country -->
                        <div class="col-md-4">
                            <label for="country" class="form-label">Country</label>
                            <select name="country" id="country" class="form-control">
                                <option value="">--Select Country--</option>
                                <?php foreach ($country as $countrys) { ?>
                                    <option value="<?php echo $countrys['CountryID']; ?>">
                                        <?php echo htmlspecialchars($countrys['CountryName']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- State -->
                        <div class="col-md-4">
                            <label for="state" class="form-label">State</label>
                            <select name="state" id="state" class="form-control">
                                <option value="">--Select State--</option>
                            </select>
                        </div>

                        <!-- Address -->
                        <div class="col-md-4">
                            <label for="address" class="form-label">Address</label>
                            <textarea required name="address" id="address" class="form-control"
                                label="address"></textarea>
                        </div>

                        <!-- GST Number -->
                        <div class="col-md-4">
                            <label for="gst_number" class="form-label">GST Number <span
                                    class="text-muted">(Optional)</span></label>
                            <input type="text" name="gst_number" placeholder="GST Number" id="gst_number"
                                class="form-control" label="gst_number" />
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-4 text-center mt-4">
                            <button type="submit" class="btn btn-primary mt-4">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function () {
        $('#country').change(function () {
            var countryId = $(this).val();

            // Clear the state dropdown
            $('#state').html('<option value="">--Select State--</option>');

            if (countryId) {
                // Fetch states using AJAX
                $.ajax({
                    url: 'fetch-states.php', // A separate PHP script for fetching states
                    type: 'POST',
                    data: { country_id: countryId },
                    dataType: 'json',
                    success: function (states) {
                        if (states.length > 0) {
                            $.each(states, function (index, state) {
                                $('#state').append(
                                    '<option value="' + state.StateID + '">' + state.StateName + '</option>'
                                );
                            });
                            if (states.length === 1 && (states[0].StateName || '').toLowerCase() === 'international') {
                                $('#state').val(states[0].StateID);
                            }
                        } else {
                            $('#state').html('<option value="">No states available</option>');
                        }
                    },
                    error: function () {
                        alert('Error fetching states.');
                    },
                });
            }
        });

        // Client-side validation (submit)
        $('#basic-form').on('submit', function (e) {
            // Let browser handle required fields validation first
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            function fail(msg) {
                alert(msg);
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            var clientName = $('#client_name').val().trim();
            var concernPerson = $('#concern_person').val().trim();
            var cin = ($('#cin').val() || '').trim();
            var website = ($('#website').val() || '').trim();
            var email = ($('#email').val() || '').trim();
            var mobile = ($('#mobile').val() || '').trim();
            var country = $('#country').val();
            var state = $('#state').val();
          
            // Logo is optional, no validation needed

            if (!clientName) return fail('Client Name is required.');
            if (!concernPerson) return fail('Concern Person is required.');


            if (!website) return fail('Website is required.');

            var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email) return fail('Email is required.');
            if (!emailRe.test(email)) return fail('Invalid email address.');

            if (!mobile) return fail('Mobile is required.');
            var digits = mobile.replace(/\D+/g, '');
            if (!/^\d+$/.test(digits) || digits.length < 10 || digits.length > 15) return fail('Invalid mobile number.');

            if (!country) return fail('Country is required.');
            if (!state) return fail('State is required.');


        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

<?php include("EvaraFroentend/layout/footer.php"); ?>
