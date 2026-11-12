<?php
include 'includes/auth.php';
include 'connection.php';
include 'includes/helpers.php';

$edit = null;
if (!empty($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $r = $con->query("SELECT * FROM companymaster WHERE CompanyID = $id");
    $edit = $r ? $r->fetch_assoc() : null;
}
$showForm = !empty($_GET['add']) || $edit !== null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyId = (int) ($_POST['company_id'] ?? 0);
    $fields = [
        'CompanyName' => trim($_POST['company_name'] ?? ''),
        'CompanyCode' => trim($_POST['company_code'] ?? ''),
        'EmailAddress' => trim($_POST['email'] ?? ''),
        'MobileNo' => trim($_POST['mobile'] ?? ''),
        'PhoneNo' => trim($_POST['phone'] ?? ''),
        'Website' => trim($_POST['website'] ?? ''),
        'Address' => trim($_POST['address'] ?? ''),
        'RegistrationNo' => strtoupper(str_replace(' ', '', trim($_POST['cin'] ?? ''))),
        'PanNo' => strtoupper(str_replace(' ', '', trim($_POST['pan'] ?? ''))),
        'GSTNumber' => strtoupper(str_replace(' ', '', trim($_POST['gst_number'] ?? ''))),
        'IECNo' => strtoupper(str_replace(' ', '', trim($_POST['iec_no'] ?? ''))),
        'LUTNo' => strtoupper(str_replace(' ', '', trim($_POST['lut_no'] ?? ''))),
        'TANNo' => strtoupper(str_replace(' ', '', trim($_POST['tan_no'] ?? ''))),
        'Bank1AccountName' => trim($_POST['bank1_account_name'] ?? ''),
        'Bank1AccountNo' => trim($_POST['bank1_account_no'] ?? ''),
        'Bank1BankName' => trim($_POST['bank1_bank_name'] ?? ''),
        'Bank1IFSC' => strtoupper(str_replace(' ', '', trim($_POST['bank1_ifsc'] ?? ''))),
        'Bank1MICR' => trim($_POST['bank1_micr'] ?? ''),
        'Bank1SwiftCode' => strtoupper(str_replace(' ', '', trim($_POST['bank1_swift'] ?? ''))),
        'Bank1Branch' => trim($_POST['bank1_branch'] ?? ''),
        'Bank1BranchCode' => trim($_POST['bank1_branch_code'] ?? ''),
        'Bank2AccountName' => trim($_POST['bank2_account_name'] ?? ''),
        'Bank2AccountNo' => trim($_POST['bank2_account_no'] ?? ''),
        'Bank2BankName' => trim($_POST['bank2_bank_name'] ?? ''),
        'Bank2IFSC' => strtoupper(str_replace(' ', '', trim($_POST['bank2_ifsc'] ?? ''))),
        'Bank2MICR' => trim($_POST['bank2_micr'] ?? ''),
        'Bank2SwiftCode' => strtoupper(str_replace(' ', '', trim($_POST['bank2_swift'] ?? ''))),
        'Bank2Branch' => trim($_POST['bank2_branch'] ?? ''),
        'Bank2BranchCode' => trim($_POST['bank2_branch_code'] ?? ''),
    ];
    $err = '';

    if ($fields['CompanyName'] === '') {
        $err = 'Company name is required.';
    } elseif ($fields['EmailAddress'] !== '' && !filter_var($fields['EmailAddress'], FILTER_VALIDATE_EMAIL)) {
        $err = 'Invalid email address.';
    } elseif ($fields['MobileNo'] !== '') {
        $mobileDigits = preg_replace('/\D+/', '', $fields['MobileNo']);
        if (strlen($mobileDigits) < 10 || strlen($mobileDigits) > 15) {
            $err = 'Invalid mobile number.';
        } else {
            $fields['MobileNo'] = $mobileDigits;
        }
    }

    if ($err === '' && $fields['GSTNumber'] !== '' && !preg_match('/^[A-Z0-9]{10,25}$/', $fields['GSTNumber'])) {
        $err = 'Invalid GST Number.';
    }
    if ($err === '' && $fields['RegistrationNo'] !== '' && !preg_match('/^[A-Z0-9]{10,25}$/', $fields['RegistrationNo'])) {
        $err = 'Invalid CIN / Registration Number.';
    }
    if ($err === '' && $fields['PanNo'] !== '' && !preg_match('/^[A-Z0-9]{10,15}$/', $fields['PanNo'])) {
        $err = 'Invalid PAN Number.';
    }
    if ($err === '' && $fields['IECNo'] !== '' && !preg_match('/^[A-Z0-9]{5,30}$/', $fields['IECNo'])) {
        $err = 'Invalid IEC Number.';
    }
    if ($err === '' && $fields['LUTNo'] !== '' && !preg_match('/^[A-Z0-9]{5,50}$/', $fields['LUTNo'])) {
        $err = 'Invalid LUT Number.';
    }
    if ($err === '' && $fields['TANNo'] !== '' && !preg_match('/^[A-Z0-9]{5,30}$/', $fields['TANNo'])) {
        $err = 'Invalid TAN Number.';
    }

    if ($err === '' && !empty($_FILES['company_logo']['name'])) {
        $uploadedLogo = ni_upload_file($_FILES['company_logo'], 'company_logos', ['png', 'jpg', 'jpeg', 'webp']);
        if ($uploadedLogo === false) {
            $err = 'Invalid logo file. Allowed: PNG, JPEG, JPG, WEBP.';
        } else {
            $fields['CompanyLogo'] = $uploadedLogo;
        }
    }

    if ($err === '') {
        $escaped = [];
        foreach ($fields as $key => $value) {
            $escaped[$key] = "'" . ni_escape($con, $value) . "'";
        }

        if ($companyId > 0) {
            $sets = [];
            foreach ($escaped as $key => $value) {
                $sets[] = "$key = $value";
            }
            $con->query("UPDATE companymaster SET " . implode(', ', $sets) . " WHERE CompanyID = $companyId");
        } else {
            $escaped['DelStatus'] = '1';
            $columns = implode(', ', array_keys($escaped));
            $values = implode(', ', array_values($escaped));
            $con->query("INSERT INTO companymaster ($columns) VALUES ($values)");
        }
        header('Location: company-master.php?saved=1');
        exit;
    }

    header('Location: company-master.php?error=' . urlencode($err) . ($companyId > 0 ? '&edit=' . $companyId : ''));
    exit;
}

if (!empty($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $con->query("UPDATE companymaster SET DelStatus = 0 WHERE CompanyID = $id");
    header('Location: company-master.php?saved=1');
    exit;
}

if (!empty($_GET['activate'])) {
    $id = (int) $_GET['activate'];
    $con->query("UPDATE companymaster SET DelStatus = 1 WHERE CompanyID = $id");
    header('Location: company-master.php?saved=1');
    exit;
}

$list = $con->query('SELECT * FROM companymaster ORDER BY DelStatus DESC, CompanyName');
include 'EvaraFroentend/layout/head.php';
include 'EvaraFroentend/layout/header.php';
?>
<style>
    .cm-section {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 20px 20px 5px;
        margin-bottom: 20px;
        background: #fbfbfc;
    }

    .cm-section h6 {
        font-weight: 700;
        color: #17a2b8;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
    }

    .cm-section h6 i {
        margin-right: 6px;
    }

    .card .card-header {
        background: #ffffff;
        border-bottom: 2px solid #17a2b8;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    label.form-label {
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 4px;
    }

    .form-control:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15);
    }

    .table thead th {
        background-color: #f5f7fa;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .table td,
    .table th {
        vertical-align: middle;
        font-size: 14px;
    }

    .action-btns a {
        display: inline-block;
        margin-right: 6px;
    }

    .invalid-feedback {
        font-size: 12px;
    }
</style>

<div class="container mt-15">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Company Master</h3>
            <div>
                <?php if ($showForm): ?>
                    <a href="company-master.php" class="btn btn-outline-secondary btn-sm">Back To List</a>
                <?php else: ?>
                    <a href="company-master.php?add=1#companyForm" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Company</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($_GET['saved'])): ?>
                <div class="alert alert-success">Saved successfully.</div><?php endif; ?>
            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company</th>
                            <th>Code</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>GST</th>
                            <th>IEC</th>
                            <th>LUT</th>
                            <th>TAN</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($row = $list->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['CompanyName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['CompanyCode'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['EmailAddress'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['MobileNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['GSTNumber'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['IECNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['LUTNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['TANNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php if ((int) ($row['DelStatus'] ?? 0) === 1): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-btns">
                                    <a href="company-master.php?edit=<?php echo (int) $row['CompanyID']; ?>#companyForm"
                                        class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                                    <?php if ((int) ($row['DelStatus'] ?? 0) === 1): ?>
                                        <a href="company-master.php?delete=<?php echo (int) $row['CompanyID']; ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Deactivate this company?');" data-bs-toggle="tooltip" data-bs-placement="top" title="Deactivate"><i class="fas fa-times-circle"></i></a>
                                    <?php else: ?>
                                        <a href="company-master.php?activate=<?php echo (int) $row['CompanyID']; ?>"
                                            class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Activate"><i class="fas fa-check-circle"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($i === 1): ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">No companies found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($showForm): ?>
            <div id="companyForm" class="mb-3">
                <h4 class="mb-1"><?php echo $edit ? 'Edit Company' : 'Add New Company'; ?></h4>
                <p class="text-muted mb-0">Invoice mein use hone wali company details yahan maintain hongi.</p>
            </div>

            <form method="post" class="row g-3 mb-4 needs-validation" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="company_id" value="<?php echo (int) ($edit['CompanyID'] ?? 0); ?>">

                <div class="cm-section col-12">
                    <h6>Basic Details</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control"
                                placeholder="e.g. Neural Info Pvt Ltd" required maxlength="150"
                                value="<?php echo htmlspecialchars($edit['CompanyName'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">Company name is required.</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Code</label>
                            <input type="text" name="company_code" class="form-control" placeholder="e.g. NI001"
                                maxlength="20"
                                value="<?php echo htmlspecialchars($edit['CompanyCode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Invoice Logo</label>
                            <input type="file" name="company_logo" class="form-control"
                                accept="image/png,image/jpeg,image/jpg,image/webp">
                            <?php if (!empty($edit['CompanyLogo'])): ?>
                                <small class="text-muted d-block mt-1">Current:
                                    <?php echo htmlspecialchars($edit['CompanyLogo'], ENT_QUOTES, 'UTF-8'); ?></small>
                            <?php endif; ?>
                            <div class="invalid-feedback">Only PNG, JPG, JPEG, WEBP allowed.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                placeholder="e.g. accounts@company.com"
                                value="<?php echo htmlspecialchars($edit['EmailAddress'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mobile</label>
                            <input type="tel" name="mobile" class="form-control" placeholder="e.g. 9876543210"
                                pattern="[0-9]{10,15}" inputmode="numeric" maxlength="15"
                                value="<?php echo htmlspecialchars($edit['MobileNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">Enter a valid mobile number (10-15 digits).</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g. 011-23456789"
                                maxlength="20"
                                value="<?php echo htmlspecialchars($edit['PhoneNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control"
                                placeholder="e.g. https://www.company.com"
                                value="<?php echo htmlspecialchars($edit['Website'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">Please enter a valid URL.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">CIN / Registration No</label>
                            <input type="text" name="cin" class="form-control text-uppercase"
                                placeholder="e.g. L12345MH2001PLC123456" pattern="[A-Za-z0-9]{10,25}" maxlength="25"
                                value="<?php echo htmlspecialchars($edit['RegistrationNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">10-25 alphanumeric characters only.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">PAN No</label>
                            <input type="text" name="pan" class="form-control text-uppercase"
                                placeholder="e.g. ABCDE1234F" pattern="[A-Za-z0-9]{10,15}" maxlength="15"
                                value="<?php echo htmlspecialchars($edit['PanNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">10-15 alphanumeric characters only.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_number" class="form-control text-uppercase"
                                placeholder="e.g. 07ABCDE1234F1Z5" pattern="[A-Za-z0-9]{10,25}" maxlength="25"
                                value="<?php echo htmlspecialchars($edit['GSTNumber'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">10-25 alphanumeric characters only.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IEC No</label>
                            <input type="text" name="iec_no" class="form-control text-uppercase"
                                placeholder="e.g. AACCN4385L" pattern="[A-Za-z0-9]{5,30}" maxlength="30"
                                value="<?php echo htmlspecialchars($edit['IECNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">5-30 alphanumeric characters only.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">LUT No</label>
                            <input type="text" name="lut_no" class="form-control text-uppercase"
                                placeholder="e.g. AD0909210147432" pattern="[A-Za-z0-9]{5,50}" maxlength="50"
                                value="<?php echo htmlspecialchars($edit['LUTNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">5-50 alphanumeric characters only.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">TAN</label>
                            <input type="text" name="tan_no" class="form-control text-uppercase"
                                placeholder="e.g. KNPN01596A" pattern="[A-Za-z0-9]{5,30}" maxlength="30"
                                value="<?php echo htmlspecialchars($edit['TANNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">5-30 alphanumeric characters only.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"
                                placeholder="Registered office address"><?php echo htmlspecialchars($edit['Address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="cm-section col-12">
                    <h6>Bank Account 1</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">A/c Name</label>
                            <input type="text" name="bank1_account_name" class="form-control"
                                placeholder="Account holder name"
                                value="<?php echo htmlspecialchars($edit['Bank1AccountName'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Account No</label>
                            <input type="text" name="bank1_account_no" class="form-control"
                                placeholder="e.g. 123456789012" inputmode="numeric"
                                value="<?php echo htmlspecialchars($edit['Bank1AccountNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank1_bank_name" class="form-control"
                                placeholder="e.g. State Bank of India"
                                value="<?php echo htmlspecialchars($edit['Bank1BankName'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IFSC</label>
                            <input type="text" name="bank1_ifsc" class="form-control text-uppercase"
                                placeholder="e.g. SBIN0001234" pattern="[A-Za-z]{4}0[A-Za-z0-9]{6}" maxlength="11"
                                value="<?php echo htmlspecialchars($edit['Bank1IFSC'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">Format: 4 letters + 0 + 6 alphanumeric.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">MICR</label>
                            <input type="text" name="bank1_micr" class="form-control" placeholder="e.g. 110002345"
                                maxlength="9"
                                value="<?php echo htmlspecialchars($edit['Bank1MICR'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Swift Code</label>
                            <input type="text" name="bank1_swift" class="form-control text-uppercase"
                                placeholder="e.g. SBININBBXXX" maxlength="11"
                                value="<?php echo htmlspecialchars($edit['Bank1SwiftCode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Branch Code</label>
                            <input type="text" name="bank1_branch_code" class="form-control" placeholder="e.g. 001234"
                                value="<?php echo htmlspecialchars($edit['Bank1BranchCode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Branch</label>
                            <input type="text" name="bank1_branch" class="form-control"
                                placeholder="e.g. Connaught Place, New Delhi"
                                value="<?php echo htmlspecialchars($edit['Bank1Branch'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>
                </div>

                <div class="cm-section col-12">
                    <h6>Bank Account 2 (Optional)</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">A/c Name</label>
                            <input type="text" name="bank2_account_name" class="form-control"
                                placeholder="Account holder name"
                                value="<?php echo htmlspecialchars($edit['Bank2AccountName'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Account No</label>
                            <input type="text" name="bank2_account_no" class="form-control"
                                placeholder="e.g. 123456789012" inputmode="numeric"
                                value="<?php echo htmlspecialchars($edit['Bank2AccountNo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank2_bank_name" class="form-control" placeholder="e.g. HDFC Bank"
                                value="<?php echo htmlspecialchars($edit['Bank2BankName'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IFSC</label>
                            <input type="text" name="bank2_ifsc" class="form-control text-uppercase"
                                placeholder="e.g. HDFC0001234" pattern="[A-Za-z]{4}0[A-Za-z0-9]{6}" maxlength="11"
                                value="<?php echo htmlspecialchars($edit['Bank2IFSC'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="invalid-feedback">Format: 4 letters + 0 + 6 alphanumeric.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">MICR</label>
                            <input type="text" name="bank2_micr" class="form-control" placeholder="e.g. 110002345"
                                maxlength="9"
                                value="<?php echo htmlspecialchars($edit['Bank2MICR'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Swift Code</label>
                            <input type="text" name="bank2_swift" class="form-control text-uppercase"
                                placeholder="e.g. HDFCINBBXXX" maxlength="11"
                                value="<?php echo htmlspecialchars($edit['Bank2SwiftCode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Branch Code</label>
                            <input type="text" name="bank2_branch_code" class="form-control" placeholder="e.g. 001234"
                                value="<?php echo htmlspecialchars($edit['Bank2BranchCode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Branch</label>
                            <input type="text" name="bank2_branch" class="form-control"
                                placeholder="e.g. MG Road, Bengaluru"
                                value="<?php echo htmlspecialchars($edit['Bank2Branch'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>
                </div>

                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-primary w-100"><?php echo $edit ? 'Update' : 'Add'; ?></button>
                </div>
                <?php if ($edit): ?>
                    <div class="col-md-2 align-self-end">
                        <a href="company-master.php" class="btn btn-secondary w-100">Cancel</a>
                    </div>
                <?php endif; ?>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    (function () {
        'use strict';

        // Auto-uppercase certain fields as the user types (matches server-side strtoupper)
        document.querySelectorAll('input.text-uppercase').forEach(function (el) {
            el.addEventListener('input', function () {
                var pos = el.selectionStart;
                el.value = el.value.toUpperCase();
                el.setSelectionRange(pos, pos);
            });
        });

        // Bootstrap-style client-side validation before submit
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<?php include 'EvaraFroentend/layout/footer.php'; ?>
