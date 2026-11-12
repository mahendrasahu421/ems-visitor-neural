<?php
include 'connection.php';
include 'includes/auth.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';
require_once 'includes/smtp-mailer.php'; // Mail configuration reader

// Ensure sent_at column exists in invoice table for tracking
$checkSentCol = $con->query("SHOW COLUMNS FROM invoice LIKE 'sent_at'");
if ($checkSentCol && $checkSentCol->num_rows === 0) {
    $con->query("ALTER TABLE invoice ADD COLUMN sent_at DATETIME NULL DEFAULT NULL AFTER status");
}

$invoiceId = (int) ($_GET['invoice_id'] ?? $_POST['invoice_id'] ?? 0);
if ($invoiceId <= 0) {
    die('Invalid invoice');
}

$sql = "SELECT i.*, cm.ClientName, cm.EmailAddress, w.invoiceNumber, co.CompanyName, co.EmailAddress AS CompanyEmail
        FROM invoice i
        JOIN clientmaster cm ON i.ClientID = cm.ClientID
        LEFT JOIN companymaster co ON cm.CompanyID = co.CompanyID
        LEFT JOIN workamount w ON i.id = w.invoice_id
        WHERE i.id = $invoiceId
        LIMIT 1";
$row = $con->query($sql)->fetch_assoc();
if (!$row) {
    die('Invoice not found');
}

$message = '';
$error = '';
$mailConfig = ni_mail_config();
$companyName = $row['CompanyName'] ?: 'Neural Informatics';
$companyEmail = $mailConfig['from_address'] ?: ($row['CompanyEmail'] ?: 'billing@neuralinformatics.com');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = filter_var($_POST['email_to'] ?? '', FILTER_VALIDATE_EMAIL);
    $subject = trim($_POST['subject'] ?? 'Proforma Invoice from ' . $companyName);
    $body = trim($_POST['body'] ?? '');
    if (!$to) {
        $error = 'Valid email required.';
    } else {
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
            . '://' . $_SERVER['HTTP_HOST']
            . dirname($_SERVER['SCRIPT_NAME']);
        $previewLink = rtrim($baseUrl, '/') . '/view-invoice.php?invoice_id=' . $invoiceId;
        $fullBody = $body . "\n\nView invoice: <a href='" . $previewLink . "'>" . $previewLink . "</a>";

        $mail = new PHPMailer(true);
        try {
            // Server settings through PHPMailer
            $mail->isSMTP();
            $mail->Host = $mailConfig['host'];
            $mail->SMTPAuth = true;
            $mail->AuthType = 'LOGIN';
            $mail->Username = $mailConfig['username'];
            $mail->Password = $mailConfig['password'];
            $mail->SMTPSecure = $mailConfig['encryption'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $mailConfig['port'];
            $mail->CharSet = 'UTF-8';
            if (!empty($mailConfig['debug'])) {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->Debugoutput = function ($debugLine, $level) {
                    error_log('PHPMailer SMTP debug [' . $level . ']: ' . $debugLine);
                };
            }

            //Recipients
            $mail->setFrom($mailConfig['from_address'], $mailConfig['from_name']);
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br($fullBody);
            $mail->AltBody = strip_tags($fullBody);

            $mail->send();

            $con->query("UPDATE invoice SET sent_at = NOW() WHERE id = $invoiceId");
            $message = 'Invoice link sent to ' . htmlspecialchars($to);
        } catch (Exception $e) {
            $error = "Mail could not be sent. Mailer Error: {$mail->ErrorInfo}";
            if (stripos($mail->ErrorInfo, 'authenticate') !== false) {
                $error .= ' SMTP authentication failed. Check the Gmail account security settings and use a valid App Password.';
            }
        }
    }
}

include 'EvaraFroentend/layout/head.php';
include 'EvaraFroentend/layout/header.php';
?>
<div class="container mt-15">
    <div class="card">
        <div class="card-header">
            <h3>Send Invoice to Client</h3>
        </div>
        <div class="card-body">
            <p><strong>Invoice:</strong> <?php echo htmlspecialchars($row['invoiceNumber']); ?>
                | <strong>Client:</strong> <?php echo htmlspecialchars($row['ClientName']); ?></p>
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <div class="alert alert-info">
                PHPMailer sender: <strong><?php echo htmlspecialchars($companyEmail); ?></strong>
            </div>
            <form method="post">
                <input type="hidden" name="invoice_id" value="<?php echo $invoiceId; ?>">
                <div class="mb-3">
                    <label>To Email</label>
                    <input type="email" name="email_to" class="form-control" required
                        value="<?php echo htmlspecialchars($row['EmailAddress'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control"
                        value="Proforma Invoice <?php echo htmlspecialchars($row['invoiceNumber']); ?>">
                </div>
                <div class="mb-3">
                    <label>Message</label>
                    <textarea name="body" class="form-control" rows="5">Dear Sir/Madam,

Please find your proforma invoice link below. Kindly process payment as per terms.

Regards,
<?php echo htmlspecialchars($companyName); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Email</button>
                <a href="view-invoice.php?invoice_id=<?php echo $invoiceId; ?>" class="btn btn-secondary">Preview</a>
                <a href="client-invoice-list.php" class="btn btn-link">Back to List</a>
            </form>
        </div>
    </div>
</div>
<?php include 'EvaraFroentend/layout/footer.php'; ?>
