<?php
// ============================================
// FILE: save-client.php (Fixed for optional fields - CIN, GST, LOGO)
// ============================================
include 'connection.php';
include 'includes/helpers.php';

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $CompanyID = 11;
    $ProductCategoryType = null;
    $CategoryType = null;
    $ClientCode = '';
    $Priority = null;
    $source = null;

    // Form fields
    $ClientName = trim($_POST['client_name'] ?? '');
    $ConcernOwner = trim($_POST['concern_person'] ?? '');
    $Fax = trim($_POST['cin'] ?? ''); // CIN - Optional
    $website = trim($_POST['website'] ?? '');
    $email_address = trim($_POST['email'] ?? '');
    $MobileNo = trim($_POST['mobile'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $state_id = isset($_POST['state']) ? trim((string) $_POST['state']) : null;
    $Address = trim($_POST['address'] ?? '');
    $gst_number = trim($_POST['gst_number'] ?? ''); // GST - Optional
    $action = $_POST['action'] ?? 'create';
    $client_id = isset($_POST['client_id']) ? (int) $_POST['client_id'] : 0;

    $err = '';

    // Validation for required fields only
    if ($ClientName === '') {
        $err = 'Client Name is required.';
    } elseif ($ConcernOwner === '') {
        $err = 'Concern Person is required.';
    } elseif ($website === '') {
        $err = 'Website is required.';
    } elseif ($email_address === '') {
        $err = 'Email is required.';
    } elseif (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $err = 'Invalid email address.';
    } elseif ($MobileNo === '') {
        $err = 'Mobile is required.';
    } else {
        $mobileDigits = preg_replace('/\D+/', '', $MobileNo);
        if (strlen($mobileDigits) < 10 || strlen($mobileDigits) > 15) {
            $err = 'Invalid mobile number.';
        } else {
            $MobileNo = $mobileDigits;
        }
    }

    if ($err === '') {
        if ($country === '' || $country === '0') {
            $err = 'Country is required.';
        }
    }

    if ($err === '') {
        if (!ni_country_is_india($con, $country)) {
            $state_id = (string) ni_get_international_state_id($con, $country);
            if ($state_id === '0') {
                $err = 'International state could not be set.';
            }
        } elseif ($state_id === null || $state_id === '' || $state_id === '0') {
            $err = 'State is required.';
        }
    }

    if ($err === '' && $Address === '') {
        $err = 'Address is required.';
    }

    // CIN Validation (Optional) - If provided, validate format
    if ($err === '' && !empty($Fax)) {
        $cinClean = strtoupper(str_replace(' ', '', $Fax));
        if (!preg_match('/^[A-Z0-9]{10,21}$/', $cinClean)) {
            $err = 'Invalid CIN. Format: Alphanumeric, 10-21 characters';
        } else {
            $Fax = $cinClean;
        }
    }

    // GST Validation (Optional) - If provided, validate format
    if ($err === '' && !empty($gst_number)) {
        $gstClean = strtoupper(str_replace(' ', '', $gst_number));
        // Standard GST format
        if (!preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{1}Z[A-Z0-9]{1}$/', $gstClean)) {
            $err = 'Invalid GST Number. Format: 15 characters (e.g., 22AAAAA0000A1Z5)';
        } else {
            $gst_number = $gstClean;
        }
    }

    if ($err !== '') {
        $redirectUrl = 'client.php?' . ($action === 'update' && $client_id > 0 ? 'edit=' . $client_id : 'add=1');
        header('Location: ' . $redirectUrl . '&error=' . urlencode($err));
        exit;
    }

    // Handle logo upload (Optional)
    $client_logo = '';
    $old_logo = '';

    if ($action === 'update' && $client_id > 0) {
        $stmt = $con->prepare("SELECT client_logo FROM clientmaster WHERE ClientID = ?");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $old_logo = $row['client_logo'] ?? '';
        }
        $stmt->close();
    }

    // Upload new logo only if a file is provided
    if (!empty($_FILES['client_logo']['name'])) {
        $uploaded_logo = ni_upload_file(
            $_FILES['client_logo'],
            'client_logos',
            ['png', 'jpg', 'jpeg', 'webp']
        );

        if ($uploaded_logo === false) {
            $redirectUrl = ($action === 'update' && $client_id > 0)
                ? 'create-new-client.php?id=' . $client_id . '&error=Invalid logo file. Allowed: PNG, JPEG, JPG, WEBP'
                : 'create-new-client.php?error=Invalid logo file. Allowed: PNG, JPEG, JPG, WEBP';
            header('Location: ' . $redirectUrl);
            exit;
        }
        $client_logo = $uploaded_logo;
    }

    $created_at = date('Y-m-d H:i:s');
    $del_status = 1;

    // Escape all values and set defaults for optional fields
    $ClientName_esc = $con->real_escape_string($ClientName);
    $ConcernOwner_esc = $con->real_escape_string($ConcernOwner);
    $Address_esc = $con->real_escape_string($Address);
    $MobileNo_esc = $con->real_escape_string($MobileNo);
    $Fax_esc = $con->real_escape_string($Fax); // Will be empty string if not provided
    $website_esc = $con->real_escape_string($website);
    $email_esc = $con->real_escape_string($email_address);
    $country_esc = $con->real_escape_string($country);
    $state_esc = $con->real_escape_string($state_id);

    $gst_esc = $con->real_escape_string($gst_number);
    $logo_to_use = !empty($client_logo) ? $client_logo : $old_logo;
    $logo_esc = $con->real_escape_string($logo_to_use);

    if ($action === 'create') {
        $sql = "INSERT INTO clientmaster
        (CompanyID, ProductCategoryType, CategoryType, ClientCode, Priority, Source,
         ClientName, ConcernOwner, Address, MobileNo, Fax, website, client_logo,
         gst_number, EmailAddress, Country, StateID, DelStatus, created_at)
        VALUES
        ($CompanyID, NULL, NULL, '$ClientCode', NULL, NULL,
         '$ClientName_esc', '$ConcernOwner_esc', '$Address_esc', '$MobileNo_esc', 
         '$Fax_esc', '$website_esc', '$logo_esc',
         '$gst_esc', '$email_esc', '$country_esc', '$state_esc', $del_status, '$created_at')";

        if ($con->query($sql) === TRUE) {
            header('Location: client.php?success=Client created successfully');
            exit;
        } else {
            header('Location: create-new-client.php?error=Database error: ' . urlencode($con->error));
            exit;
        }
    } elseif ($action === 'update' && $client_id > 0) {
        $sql = "UPDATE clientmaster SET 
            ClientName = '$ClientName_esc',
            ConcernOwner = '$ConcernOwner_esc',
            Address = '$Address_esc',
            MobileNo = '$MobileNo_esc',
            Fax = '$Fax_esc',
            website = '$website_esc',
            client_logo = '$logo_esc',
            gst_number = '$gst_esc',
            EmailAddress = '$email_esc',
            Country = '$country_esc',
            StateID = '$state_esc'
            WHERE ClientID = $client_id AND DelStatus = 1";

        if ($con->query($sql) === TRUE) {
            if (!empty($client_logo) && !empty($old_logo) && $client_logo !== $old_logo && file_exists($old_logo)) {
                @unlink($old_logo);
            }
            header('Location: client.php?success=Client updated successfully');
            exit;
        } else {
            header('Location: create-new-client.php?id=' . $client_id . '&error=Database error: ' . urlencode($con->error));
            exit;
        }
    }
}

header('Location: client.php');
exit;
?>