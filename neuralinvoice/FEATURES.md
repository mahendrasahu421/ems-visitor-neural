# Neural Invoice — Feature Map

## A) Masters
| Feature | Status | File |
|---------|--------|------|
| Country Master | ✅ New | `country-master.php` |
| State Master | ✅ New | `state-master.php` |

## B) Client
| Field | Status |
|-------|--------|
| Client Name, Concern, CIN, Website, Email, Mobile | ✅ `create-new-client.php` |
| Country, State, Address, GST | ✅ |
| Logo upload | ✅ New (run DB upgrade first) |

## C) Client Project (AMC / payment breakup)
| Feature | Status | File |
|---------|--------|------|
| Client-wise projects | ✅ | `create-client-project.php` |
| Payment % wise (30-40-30) | ✅ | Percentage mode |
| Module-wise payment | ✅ | Module mode |
| Proposal upload | ✅ | PDF/DOC in `uploads/proposals/` |
| AMC Financial Year (1 Apr – 31 Mar) | ✅ | FY dropdown — dates auto |
| Working days (Mon–Fri, 5-day week) | ✅ | `ajax-amc-fy-days.php` |
| Work Type (dynamic from DB) | ✅ | `work-type-master.php` |
| AMC dates within FY (user selects) | ✅ | Start/End date pickers |
| Mark milestone paid | ✅ | `view-client-project.php` |

## D) Proforma Invoice
| Feature | Status | File |
|---------|--------|------|
| Invoice Type Old / New | ✅ | `create-newInvoice.php` |
| Auto number NISPL-25-26/PI001 | ✅ | `invoice-type.php` |
| Self details, T&C, dates | ✅ | |
| Multi row description/amount | ✅ | |
| GST UP 18% (CGST+SGST 9%) | ✅ | |
| Outside UP IGST 18% | ✅ | |
| International no tax | ✅ | State = internation |

## E) Invoice list & payments
| Feature | Status | File |
|---------|--------|------|
| List + search + date filter | ✅ | `client-invoice-list.php` |
| Filter by client | ✅ New | |
| Preview | ✅ | `view-invoice.php` |
| Send to client email | ✅ New | `send-invoice.php` |
| Received payment + TDS | ✅ | `update-payment.php` |
| Client Ledger report | ✅ New | `client-ledger.php` |

## Setup
1. Import / use database `neurab2l_emsDB` in `connection.php`
2. Open once: `http://localhost/neuralinvoice/run-schema-upgrade.php`
3. Login via `index.php` → dashboard `what-you-want.php`
