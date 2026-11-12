<?php

function ni_invoice_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function ni_invoice_company_select_sql(): string
{
    return "
        co.CompanyName AS BillCompanyName,
        co.CompanyCode AS BillCompanyCode,
        co.CompanyLogo AS BillCompanyLogo,
        co.Address AS BillCompanyAddress,
        co.RegistrationNo AS BillCompanyCIN,
        co.PanNo AS BillCompanyPAN,
        co.GSTNumber AS BillCompanyGST,
        co.IECNo AS BillCompanyIEC,
        co.LUTNo AS BillCompanyLUT,
        co.TANNo AS BillCompanyTAN,
        co.EmailAddress AS BillCompanyEmail,
        co.MobileNo AS BillCompanyMobile,
        co.PhoneNo AS BillCompanyPhone,
        co.Website AS BillCompanyWebsite,
        co.Bank1AccountName AS BillBank1AccountName,
        co.Bank1AccountNo AS BillBank1AccountNo,
        co.Bank1BankName AS BillBank1BankName,
        co.Bank1IFSC AS BillBank1IFSC,
        co.Bank1MICR AS BillBank1MICR,
        co.Bank1SwiftCode AS BillBank1SwiftCode,
        co.Bank1Branch AS BillBank1Branch,
        co.Bank1BranchCode AS BillBank1BranchCode,
        co.Bank2AccountName AS BillBank2AccountName,
        co.Bank2AccountNo AS BillBank2AccountNo,
        co.Bank2BankName AS BillBank2BankName,
        co.Bank2IFSC AS BillBank2IFSC,
        co.Bank2MICR AS BillBank2MICR,
        co.Bank2SwiftCode AS BillBank2SwiftCode,
        co.Bank2Branch AS BillBank2Branch,
        co.Bank2BranchCode AS BillBank2BranchCode
    ";
}

function ni_invoice_company_data(array $row): array
{
    $name = (string) ($row['BillCompanyName'] ?? '');
    return [
        'name' => $name,
        'code' => $row['BillCompanyCode'] ?? '',
        'logo' => $row['BillCompanyLogo'] ?? '',
        'address' => $row['BillCompanyAddress'] ?? '',
        'cin' => $row['BillCompanyCIN'] ?? '',
        'pan' => $row['BillCompanyPAN'] ?? '',
        'gst' => $row['BillCompanyGST'] ?? '',
        'iec' => $row['BillCompanyIEC'] ?? '',
        'lut' => $row['BillCompanyLUT'] ?? '',
        'tan' => $row['BillCompanyTAN'] ?? '',
        'email' => $row['BillCompanyEmail'] ?? '',
        'mobile' => $row['BillCompanyMobile'] ?? '',
        'phone' => $row['BillCompanyPhone'] ?? '',
        'website' => $row['BillCompanyWebsite'] ?? '',
        'bank1' => [
            'title' => 'Account details 1',
            'account_name' => $row['BillBank1AccountName'] ?? '',
            'account_no' => $row['BillBank1AccountNo'] ?? '',
            'bank_name' => $row['BillBank1BankName'] ?? '',
            'ifsc' => $row['BillBank1IFSC'] ?? '',
            'micr' => $row['BillBank1MICR'] ?? '',
            'swift' => $row['BillBank1SwiftCode'] ?? '',
            'branch' => $row['BillBank1Branch'] ?? '',
            'branch_code' => $row['BillBank1BranchCode'] ?? '',
        ],
        'bank2' => [
            'title' => 'Account details 2',
            'account_name' => $row['BillBank2AccountName'] ?? '',
            'account_no' => $row['BillBank2AccountNo'] ?? '',
            'bank_name' => $row['BillBank2BankName'] ?? '',
            'ifsc' => $row['BillBank2IFSC'] ?? '',
            'micr' => $row['BillBank2MICR'] ?? '',
            'swift' => $row['BillBank2SwiftCode'] ?? '',
            'branch' => $row['BillBank2Branch'] ?? '',
            'branch_code' => $row['BillBank2BranchCode'] ?? '',
        ],
    ];
}

function ni_invoice_bank_html(array $bank): string
{
    $lines = [
        '<b>' . ni_invoice_h($bank['title']) . ':</b>',
    ];
    if (!empty($bank['account_name'])) {
        $lines[] = 'A/c Name: ' . ni_invoice_h($bank['account_name']);
    }
    if (!empty($bank['account_no'])) {
        $lines[] = 'Account No: ' . ni_invoice_h($bank['account_no']);
    }
    if (!empty($bank['bank_name'])) {
        $lines[] = 'Bank Name: ' . ni_invoice_h($bank['bank_name']);
    }
    if (!empty($bank['ifsc'])) {
        $lines[] = 'IFSC Code: ' . ni_invoice_h($bank['ifsc']);
    }
    if (!empty($bank['micr'])) {
        $lines[] = 'MICR Code: ' . ni_invoice_h($bank['micr']);
    }
    if (!empty($bank['swift'])) {
        $lines[] = 'Swift Code: ' . ni_invoice_h($bank['swift']);
    }
    if (!empty($bank['branch'])) {
        $lines[] = 'Branch: ' . ni_invoice_h($bank['branch']);
    }
    if (!empty($bank['branch_code'])) {
        $lines[] = 'Branch Code: ' . ni_invoice_h($bank['branch_code']);
    }
    return implode('<br>', $lines);
}
