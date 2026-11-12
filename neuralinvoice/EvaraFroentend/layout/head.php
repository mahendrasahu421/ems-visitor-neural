<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '././connection.php';
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <title>Neural - Your Ultimate Solution</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="Efficient and user-friendly invoice software for managing your business finances. Generate invoices, track payments, and streamline your invoicing process.">
    <meta name="keywords" content="Invoice Software, Business Invoicing, Financial Management, Online Invoicing, Invoice Generation">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="forentEndAssets/imgs/theme/favicon.svg">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="forentEndAssets/css/main.css">
</head>

<style>
    a{
        text-decoration: none;
        color:white;
    }
    .mr-1 { margin-right: .25rem !important; }
    .mr-2 { margin-right: .5rem !important; }
    .mr-3 { margin-right: 1rem !important; }
    .ml-1 { margin-left: .25rem !important; }
    .ml-2 { margin-left: .5rem !important; }
    .ml-3 { margin-left: 1rem !important; }
    .badge-primary { background-color: var(--bs-primary); color: #fff; }
    .badge-secondary { background-color: var(--bs-secondary); color: #fff; }
    .badge-success { background-color: var(--bs-success); color: #fff; }
    .badge-danger { background-color: var(--bs-danger); color: #fff; }
    .badge-warning { background-color: var(--bs-warning); color: #212529; }
    .badge-info { background-color: var(--bs-info); color: #000; }
    .badge-light { background-color: var(--bs-light); color: #212529; }
    .badge-dark { background-color: var(--bs-dark); color: #fff; }
    .form-inline { display: flex; flex-flow: row wrap; align-items: center; }
</style>
<body>


    <!-- Quick view -->
