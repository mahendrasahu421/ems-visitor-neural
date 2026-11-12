# Neural Invoice - Project Documentation

## 1. Project mein kya ho raha hai?

Neural Invoice ek PHP + MySQL based billing/accounting web application hai. Is project ka main kaam clients, companies, projects/AMC, proforma invoices, payments, tax invoices, expenses aur reports ko ek jagah manage karna hai.

Simple words mein:

- Admin login karta hai.
- Client aur company master data maintain hota hai.
- Client ke liye project ya AMC create hota hai.
- Project payment milestones mein split hota hai.
- Milestone ya custom line items ke basis par proforma invoice banta hai.
- Client ko invoice email kiya ja sakta hai.
- Payment receive hone par payment receipt/tax invoice records update hote hain.
- Dashboard aur reports turnover, received payment, pending payment, expenses aur profit/loss dikhate hain.

## 2. Tech Stack

- Backend: Plain PHP
- Database: MySQL/MariaDB
- Server environment: XAMPP style local setup
- Frontend: Bootstrap, jQuery, DataTables, Chart.js
- Email: PHPMailer with SMTP config
- File uploads: `uploads/` folder
- Dependency manager: Composer

Important dependency:

```json
{
  "phpmailer/phpmailer": "^7.1"
}
```

## 3. Basic Setup

1. Project folder ko XAMPP ke `htdocs` mein rakha gaya hai:

   ```text
   C:\xampp\htdocs\neuralinvoice
   ```

2. Database connection `connection.php` mein configured hai:

   ```php
   $host = "localhost";
   $username = "root";
   $password = "";
   $database = "neurab2l_emsDB";
   ```

3. Database schema upgrade ke liye:

   ```text
   http://localhost/neuralinvoice/run-schema-upgrade.php
   ```

4. Login page:

   ```text
   http://localhost/neuralinvoice/index.php
   ```

5. Login success ke baad user `what-you-want.php` par jata hai, jahan quick action cards milte hain.

## 4. Authentication Flow

Login flow simple session based hai.

- `index.php` login form show karta hai.
- `login.php` username/password ko `invoiceemployees` table se match karta hai.
- Match hone par `$_SESSION['username']` set hota hai.
- Protected pages `includes/auth.php` include karke session check karte hain.
- Session missing ho to user `index.php` par redirect hota hai.
- `logout.php` session clear karta hai.

Note: Password currently plain database comparison se check ho raha hai. Future mein password hashing add karna better rahega.

## 5. Main Navigation

`what-you-want.php` project ka quick launcher hai. Yahan se user major sections open karta hai:

- Dashboard
- Proforma Invoice
- Invoice List
- Add Client
- Client Project / AMC
- Client Ledger
- Payment Receipt
- Tax Invoice
- DB Schema Upgrade

## 6. Core Modules

### 6.1 Dashboard

File: `dashboard.php`

Dashboard business summary dikhata hai:

- Active companies count
- Active clients count
- Active projects/AMC count
- Proforma invoices count
- Tax invoice/payment rows count
- Selected period ka expense total
- Turnover
- Received amount
- Profit/Loss
- Due invoices
- Recent invoices
- Quick actions

Dashboard date filters support karta hai:

- Quarterly
- Half yearly
- Yearly comparison
- Custom from/to date
- Indian financial year: April 1 to March 31

Chart.js se visual charts bante hain:

- Turnover trend
- Profit/loss trend
- Year-wise comparison
- Received vs expenses

### 6.2 Company Master

File: `company-master.php`

Company Master mein invoice issue karne wali company details maintain hoti hain:

- Company name
- Company code
- Logo
- Email/mobile/phone/website
- Address
- CIN/registration number
- PAN, GST, IEC, LUT, TAN
- Bank account 1
- Bank account 2
- Active/inactive status

Company code invoice number prefix mein use hota hai. Client ke saath linked company milti hai to us company ka code prefix ban sakta hai.

### 6.3 Client Master

Files:

- `client.php`
- `create-new-client.php`
- `create-client.php`

Client Master mein client details save hoti hain:

- Client name
- Concern person
- CIN
- Website
- Email
- Mobile
- Country
- State
- Address
- GST number
- Optional logo

Country select karne ke baad states AJAX se load hote hain:

- `fetch-states.php`
- `get-states.php`
- `ajax-get-states.php`

Client country/state GST calculation mein important hai.

### 6.4 Country and State Master

Files:

- `country-master.php`
- `state-master.php`
- `ajax-get-countries.php`
- `ajax-get-states.php`
- `fetch-states.php`

Ye module client location data manage karta hai. Location ke basis par invoice/project tax decide hota hai:

- India + Uttar Pradesh: CGST 9% + SGST 9%
- India + other state: IGST 18%
- International: GST 0%, invoice flow mein bank charge 3.5%

### 6.5 Work Type Master

File: `work-type-master.php`

Work type define karta hai ki engagement normal project hai ya AMC only.

Default work types schema bootstrap mein create ho sakte hain:

- Mobile App
- Web Application
- Website
- Digital Marketing
- ERP / Software
- AMC Support

`amc_type` field se type decide hota hai:

- `project`: Project + milestones
- `amc_only`: Sirf AMC/renewal based work

### 6.6 Client Project / AMC

Files:

- `create-client-project.php`
- `client-project-list.php`
- `view-client-project.php`
- `client-project-edit.php`
- `ajax-amc-fy-days.php`

Is module mein client ke project ya AMC records bante hain.

Project flow:

1. Client select hota hai.
2. Work type select hota hai.
3. Project name/title enter hota hai.
4. Project total value enter hoti hai.
5. Project date/duration optional manage hoti hai.
6. AMC include karna ho to financial year, start date, end date aur working days calculate hote hain.
7. Payment milestones add hote hain.
8. Proposal file upload ho sakti hai.
9. Project `client_projects` table mein save hota hai.
10. Milestones `project_payment_milestones` table mein save hote hain.

Milestone payment mode:

- Percentage wise: jaise 30%, 40%, 30%
- Module wise: module name + fixed amount

AMC only flow:

- Annual AMC amount enter hota hai.
- Split plan choose hota hai:
  - Single
  - Half yearly
  - Quarterly
  - Custom
- System automatically AMC milestone rows generate kar sakta hai.

AMC dates Indian FY ke andar validate hoti hain:

```text
FY start: April 1
FY end: March 31
```

### 6.7 Proforma Invoice

Files:

- `create-newInvoice.php`
- `create-invoice-fixed.php`
- `invoice-type.php`
- `check-invoice.php`
- `get_client_details.php`
- `client-invoice-list.php`
- `view-invoice.php`
- `checkinvoice.php`
- `edit-invoice.php`
- `cancel-invoice.php`

Invoice flow:

1. User invoice page open karta hai.
2. New invoice ya old invoice choose karta hai.
3. Client select karta hai.
4. Client details AJAX se fetch hoti hain.
5. Invoice number auto-generate hota hai.
6. Self details, invoice date, due date, terms, delivery date fill hote hain.
7. Description/duration/amount rows add hoti hain.
8. Discount apply ho sakta hai.
9. System GST/bank charge calculate karta hai.
10. Invoice `invoice` table mein save hota hai.
11. Line items `workamount` table mein save hote hain.
12. Invoice list mein redirect hota hai.

Invoice number generation:

- New invoice mein prefix client ki linked company ke `CompanyCode` se aata hai.
- Fallback prefix `NSPL` hai.
- Format roughly:

```text
PREFIX/YY-YY/00ID
```

Example:

```text
NISPL/25-26/001
```

Milestone linked invoice:

- Agar invoice kisi project milestone se ban raha hai, `milestone_id` invoice ke saath save hota hai.
- Invoice create hone par milestone ka `invoiced_amount` update hota hai.

Currency:

- Default INR
- Optional USD display
- Exchange rate ke basis par foreign total calculate hota hai

International invoice:

- GST 0
- Bank charge 3.5%
- USD display auto-enable ho sakta hai

### 6.8 Send Invoice by Email

File: `send-invoice.php`

Email flow:

1. Invoice ID ke basis par invoice + client email fetch hota hai.
2. User recipient, subject aur body edit kar sakta hai.
3. PHPMailer SMTP se email send hota hai.
4. Email body mein invoice preview link include hota hai.
5. Send success par `invoice.sent_at` update hota hai.

SMTP config `.env` file se read hota hai:

```text
APP_NAME
MAIL_HOST
MAIL_PORT
MAIL_USERNAME
MAIL_PASSWORD
MAIL_ENCRYPTION
MAIL_FROM_ADDRESS
MAIL_FROM_NAME
```

Gmail use karte waqt `MAIL_PASSWORD` mein Gmail App Password required hai.

### 6.9 Payment Receipt

Files:

- `update-payment.php`
- `update-client-invoice.php`
- `cancel-invoice.php`

Payment flow:

1. `update-payment.php` invoice list dikhata hai.
2. User client filter laga sakta hai.
3. Har invoice ka total, received, TDS aur pending amount dikhata hai.
4. Pending invoice par Receive Payment modal open hota hai.
5. User received amount, TDS amount, received date aur note enter karta hai.
6. `update-client-invoice.php` payment validate karta hai.
7. Payment pending amount se zyada nahi ho sakti.
8. Payment `recived_payment` table mein insert/update hoti hai.
9. Invoice status update hota hai:
   - `1`: sent/partial/pending
   - `2`: paid
   - `3`: cancelled
10. Linked milestone paid status bhi update hota hai.

TDS handling:

- `recived_amount`: actual received amount
- `tds_amount`: TDS deducted amount
- `received_net`: received + TDS credit

Cancellation:

- Paid invoice cancel nahi hoti.
- Cancel karne ke liye reason required hai.
- Cancelled invoice par payment receive nahi hoti.

### 6.10 Tax Invoice / Received Payment Reports

Files:

- `recived-payment-form-clinet.php`
- `recived-payment-form-clinet-report.php`
- `view-tax-invoice.php`
- `pending-due-payment-report.php`
- `client-ledger.php`
- `day-book.php`

Ye reports/payment module payment ke baad account visibility provide karta hai:

- Tax invoice view
- Client wise received payment report
- Pending due payment report
- Client ledger
- Day book

### 6.11 Expenses

Files:

- `add-expencese.php`
- `addExpenseAction.php`
- `add-expencese-list.php`
- `create-expencese-categories.php`
- `create-new-expencese-categories.php`
- `expense-categories-list.php`

Expense module mein business expenses record hote hain. Dashboard profit/loss mein:

```text
Profit/Loss = Received Amount - Expenses
```

Expense data `expenses` table se aggregate hota hai.

## 7. Database Tables

Important tables jo code mein use ho rahi hain:

- `invoiceemployees`: login users
- `companymaster`: invoice issuer companies
- `clientmaster`: clients
- `countrymaster`: countries
- `statemaster`: states
- `work_type_master`: project/AMC work types
- `client_projects`: client projects and AMC records
- `project_payment_milestones`: project/AMC payment milestones
- `invoice`: proforma invoices
- `workamount`: invoice line items
- `recived_payment`: payment receipts / tax invoice records
- `expenses`: expense records

## 8. Auto Schema Upgrade

`connection.php` har request par `includes/schema-bootstrap.php` include karta hai:

```php
require_once __DIR__ . '/includes/schema-bootstrap.php';
ni_ensure_database_schema($con);
```

Iska kaam missing tables/columns create/add karna hai. Ye project ko older database versions se compatible rakhne ki koshish karta hai.

Schema bootstrap ensure karta hai:

- `companymaster` table and columns
- `clientmaster.CompanyID`
- `work_type_master`
- Default work types
- `client_projects`
- `project_payment_milestones`
- Invoice extra columns: company, bank charge, currency, sent/cancel fields
- Payment extra columns: TDS, received net, note, date

Manual upgrade file bhi hai:

- `database/schema_upgrade.sql`
- `run-schema-upgrade.php`

## 9. Business Rules

### GST Rules

Client country/state ke basis par tax calculate hota hai:

- India + Uttar Pradesh:
  - CGST 9%
  - SGST 9%
  - Total GST 18%
- India + other state:
  - IGST 18%
- International:
  - GST 0%
  - Invoice flow mein bank charge 3.5%

Related helper:

```text
ni_compute_gst_for_client_amount()
```

### Financial Year Rules

Indian financial year use hota hai:

```text
April 1 to March 31
```

Example:

```text
FY 2025-26 = 2025-04-01 to 2026-03-31
```

AMC dates selected FY ke bahar nahi ja sakti.

### Working Days

AMC working days calculate hote hain. Code comment mein Mon-Fri likha hai, but current helper `ni_count_working_days()` mein condition `dow <= 7` hai, jo Monday se Sunday sab days count kar sakta hai. Isko verify/fix karna future task ho sakta hai.

### Milestone Rules

- Project milestones ka total amount project value se zyada nahi hona chahiye.
- Percentage mode mein total percentage ideally 100% hona chahiye.
- AMC only mein split plan se milestones auto-generate ho sakte hain.
- Milestone invoice hone par `invoiced_amount` badhta hai.
- Payment complete hone par milestone paid status update hota hai.

### Payment Rules

- Payment credit = received amount + TDS amount
- Payment pending amount se zyada nahi ho sakti
- Cancelled invoice par payment allowed nahi hai
- Fully paid invoice ka status `2` set hota hai

## 10. Important File Map

```text
connection.php                  Database connection + schema bootstrap
includes/auth.php               Session login guard
includes/helpers.php            Shared helper functions
includes/schema-bootstrap.php   Auto database upgrade/columns
includes/smtp-mailer.php        SMTP config and custom SMTP helpers

index.php                       Login page
login.php                       Login submit handler
logout.php                      Logout
what-you-want.php               Quick action menu
dashboard.php                   Analytics dashboard

company-master.php              Company master
client.php                      Client list/add/edit UI
create-client.php               Client create/update handler
create-new-client.php           Older/new client create form

country-master.php              Country master
state-master.php                State master
work-type-master.php            Work type master

create-client-project.php       Project/AMC creation
client-project-list.php         Project list
view-client-project.php         Project detail and milestone actions
client-project-edit.php         Project edit

create-newInvoice.php           Proforma invoice form
create-invoice-fixed.php        Invoice save handler
invoice-type.php                Invoice number generator
client-invoice-list.php         Invoice list
view-invoice.php                Invoice preview
checkinvoice.php                Invoice preview/print view
edit-invoice.php                Invoice edit
cancel-invoice.php              Invoice cancellation
send-invoice.php                Email invoice link

update-payment.php              Payment receipt UI
update-client-invoice.php       Payment save handler
recived-payment-form-clinet.php Tax invoice/payment view
client-ledger.php               Client ledger
day-book.php                    Day book

add-expencese.php               Expense form
addExpenseAction.php            Expense save handler
add-expencese-list.php          Expense list
```

## 11. High Level Data Flow

```text
Login
  -> Quick menu / Dashboard
  -> Master setup
       -> Company
       -> Country/State
       -> Client
       -> Work Type
  -> Project / AMC
       -> Payment milestones
       -> Optional proposal upload
  -> Proforma Invoice
       -> Client + company + milestone/line items
       -> GST/bank charge
       -> Invoice + workamount rows
  -> Send Invoice
       -> SMTP email with preview link
  -> Receive Payment
       -> recived_payment
       -> invoice status update
       -> milestone paid update
  -> Reports
       -> Dashboard
       -> Ledger
       -> Due payment
       -> Tax invoice
       -> Day book
```

## 12. Current Known Notes / Risks

- `TODO.md` mein duration issue mentioned hai: `client-invoice-list.php` mein duration `Array` show ho sakta hai.
- Login currently plain SQL username/password check karta hai; password hashing and prepared statements recommended hain.
- Codebase mein kuch spelling inconsistencies hain, jaise `recived`, `expencese`, `deudate`, `discription`. In names ko change karne se pehle database/file references carefully update karne honge.
- Kaafi pages direct SQL strings use karte hain; security hardening ke liye prepared statements gradually add karna best rahega.
- External JS/CDN dependencies use ho rahi hain; offline/local environment mein internet na ho to charts/DataTables features impact ho sakte hain.
- `.env` mein SMTP password blank hai; email send karne ke liye Gmail App Password set karna hoga.

## 13. Short Summary

Ye project ek complete invoice management system hai. Isme client aur company master se data aata hai, project/AMC milestones define hote hain, un milestones ya custom line items se proforma invoice generate hota hai, GST location ke basis par calculate hota hai, payment receive hone par invoice status aur milestone status update hota hai, aur dashboard/reports business ka financial picture dikhate hain.
