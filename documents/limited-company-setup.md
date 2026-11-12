# Limited Company Deployment

Use this profile when the same EMS codebase is deployed for another company with a separate database and limited features.

## Environment

Set these values in the new company's `.env`:

```env
APP_PROFILE=limited_company
DB_DATABASE=your_new_company_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

Then run:

```bash
php artisan migrate
php artisan config:clear
```

## Enabled Features

- Admin employee section
- Employee leave section
- Admin leave request/report screen
- Admin check-in/check-out attendance report
- Employee check-in/check-out API

Other web modules are blocked with a 404 response while `APP_PROFILE=limited_company` is active.

## Attendance API

Endpoint:

```http
POST /api/employee/attendance/toggle
```

Payload:

```json
{
  "login": "employee@example.com",
  "password": "employee-password",
  "action": "auto",
  "latitude": 23.2599,
  "longitude": 77.4126,
  "note": "Office entry"
}
```

`login` can be employee email, mobile, or name. You can send `employee_id` instead of `login`.

Allowed `action` values:

- `auto`: check in if not checked in today, otherwise check out
- `checkin`: force check-in
- `checkout`: force check-out
