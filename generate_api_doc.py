import os
import json
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'''<w:tcMar {nsdecls("w")}>
        <w:top w:w="{top}" w:type="dxa"/>
        <w:bottom w:w="{bottom}" w:type="dxa"/>
        <w:left w:w="{left}" w:type="dxa"/>
        <w:right w:w="{right}" w:type="dxa"/>
    </w:tcMar>''')
    tcPr.append(tcMar)

def add_code_block(doc, code_text):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = tbl.cell(0, 0)
    set_cell_background(cell, "F3F4F6")
    set_cell_margins(cell, top=120, bottom=120, left=180, right=180)
    
    # Border
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(f'''<w:tcBorders {nsdecls("w")}>
        <w:top w:val="single" w:sz="4" w:space="0" w:color="E5E7EB"/>
        <w:left w:val="single" w:sz="18" w:space="0" w:color="0284C7"/>
        <w:bottom w:val="single" w:sz="4" w:space="0" w:color="E5E7EB"/>
        <w:right w:val="single" w:sz="4" w:space="0" w:color="E5E7EB"/>
    </w:tcBorders>''')
    tcPr.append(borders)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)
    p.paragraph_format.line_spacing = 1.15
    run = p.add_run(code_text.strip())
    run.font.name = "Consolas"
    run.font.size = Pt(9)
    run.font.color.rgb = RGBColor(31, 41, 55)
    doc.add_paragraph() # Spacing

def format_table(tbl, col_widths, headers, rows):
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl.autofit = False
    
    # Header Row
    hdr_cells = tbl.rows[0].cells
    for i, title in enumerate(headers):
        hdr_cells[i].width = Inches(col_widths[i])
        set_cell_background(hdr_cells[i], "1E3A8A")
        set_cell_margins(hdr_cells[i], top=120, bottom=120, left=140, right=140)
        p = hdr_cells[i].paragraphs[0]
        p.paragraph_format.space_before = Pt(2)
        p.paragraph_format.space_after = Pt(2)
        run = p.add_run(title)
        run.bold = True
        run.font.name = "Segoe UI"
        run.font.size = Pt(9.5)
        run.font.color.rgb = RGBColor(255, 255, 255)
    
    # Data Rows
    for r_idx, row_data in enumerate(rows):
        row_cells = tbl.add_row().cells
        bg_color = "F9FAFB" if r_idx % 2 == 1 else "FFFFFF"
        for c_idx, val in enumerate(row_data):
            row_cells[c_idx].width = Inches(col_widths[c_idx])
            set_cell_background(row_cells[c_idx], bg_color)
            set_cell_margins(row_cells[c_idx], top=80, bottom=80, left=140, right=140)
            p = row_cells[c_idx].paragraphs[0]
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            run = p.add_run(str(val))
            run.font.name = "Segoe UI"
            run.font.size = Pt(9)
            if c_idx == 0:
                run.bold = True
                run.font.color.rgb = RGBColor(30, 58, 138)
            elif c_idx == 2 and str(val).lower() == "yes":
                run.bold = True
                run.font.color.rgb = RGBColor(185, 28, 28)
            else:
                run.font.color.rgb = RGBColor(55, 65, 81)

def add_endpoint_header(doc, method, endpoint, auth_type):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(14)
    p.paragraph_format.space_after = Pt(4)
    
    # Badge for method
    m_run = p.add_run(f" {method} ")
    m_run.bold = True
    m_run.font.name = "Segoe UI"
    m_run.font.size = Pt(10)
    if method == "POST":
        m_run.font.color.rgb = RGBColor(255, 255, 255)
        # We can simulate badge or bold color
    elif method == "GET":
        m_run.font.color.rgb = RGBColor(16, 185, 129)
        
    e_run = p.add_run(f"  {endpoint}  ")
    e_run.bold = True
    e_run.font.name = "Consolas"
    e_run.font.size = Pt(11)
    e_run.font.color.rgb = RGBColor(30, 41, 59)
    
    auth_run = p.add_run(f"[{auth_type}]")
    auth_run.font.name = "Segoe UI"
    auth_run.font.size = Pt(9)
    auth_run.font.color.rgb = RGBColor(107, 114, 128)

def build_word_doc(output_path):
    doc = Document()
    
    # Page Margins
    for section in doc.sections:
        section.top_margin = Inches(0.8)
        section.bottom_margin = Inches(0.8)
        section.left_margin = Inches(0.8)
        section.right_margin = Inches(0.8)
        
    # --- DOCUMENT COVER / HEADER ---
    title_p = doc.add_paragraph()
    title_p.paragraph_format.space_before = Pt(20)
    title_p.paragraph_format.space_after = Pt(4)
    r_title = title_p.add_run("EMS Visitor & Employee Portal")
    r_title.font.name = "Segoe UI"
    r_title.font.size = Pt(26)
    r_title.bold = True
    r_title.font.color.rgb = RGBColor(30, 58, 138) # Navy Blue
    
    sub_p = doc.add_paragraph()
    sub_p.paragraph_format.space_after = Pt(16)
    r_sub = sub_p.add_run("REST API Technical Specification & Integration Manual")
    r_sub.font.name = "Segoe UI"
    r_sub.font.size = Pt(14)
    r_sub.font.color.rgb = RGBColor(2, 132, 199) # Sky Blue
    
    # Metadata Box
    meta_table = doc.add_table(rows=1, cols=4)
    meta_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    meta_widths = [1.7, 1.7, 1.7, 1.7]
    meta_data = [
        ("API Version", "v1.0 (RESTful)"),
        ("Base Protocol", "HTTPS / JSON"),
        ("Auth Type", "Sanctum Bearer"),
        ("Target Clients", "Mobile & Web Apps")
    ]
    for i, (k, v) in enumerate(meta_data):
        cell = meta_table.cell(0, i)
        cell.width = Inches(meta_widths[i])
        set_cell_background(cell, "F1F5F9")
        set_cell_margins(cell, top=80, bottom=80, left=100, right=100)
        p = cell.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        rk = p.add_run(f"{k}\n")
        rk.font.name = "Segoe UI"
        rk.font.size = Pt(8.5)
        rk.font.color.rgb = RGBColor(100, 116, 139)
        rv = p.add_run(v)
        rv.bold = True
        rv.font.name = "Segoe UI"
        rv.font.size = Pt(9.5)
        rv.font.color.rgb = RGBColor(30, 41, 59)
        
    doc.add_paragraph() # Spacing
    
    # --- EXECUTIVE OVERVIEW ---
    h1 = doc.add_heading("1. Overview & Standard Protocols", level=1)
    h1.style.font.name = "Segoe UI"
    h1.style.font.size = Pt(16)
    h1.style.font.color.rgb = RGBColor(30, 58, 138)
    
    p = doc.add_paragraph()
    p.add_run("This document provides technical API definitions for the EMS Employee Portal and Attendance Management system. These endpoints power client applications (Android, iOS, Flutter, React Native, or Web Single Page Applications).")
    
    p = doc.add_paragraph()
    p.add_run("Base URLs:\n").bold = True
    p.add_run("• Local Development: ").font.color.rgb = RGBColor(75, 85, 99)
    p.add_run("http://127.0.0.1:8000/api/v1\n")
    p.add_run("• Production Server: ").font.color.rgb = RGBColor(75, 85, 99)
    p.add_run("https://your-domain.com/api/v1\n")
    
    p = doc.add_paragraph()
    p.add_run("Standard Headers:\n").bold = True
    p.add_run("All requests MUST include the following headers:\n")
    add_code_block(doc, "Accept: application/json\nContent-Type: application/json\nAuthorization: Bearer <access_token>  (For protected endpoints)")
    
    p = doc.add_paragraph()
    p.add_run("Standard JSON Responses:\n").bold = True
    doc.add_paragraph("Success Response Envelope:")
    add_code_block(doc, json.dumps({
        "success": True,
        "message": "Operation completed successfully.",
        "data": {}
    }, indent=2))
    
    doc.add_paragraph("Error Response Envelope:")
    add_code_block(doc, json.dumps({
        "success": False,
        "message": "Validation or execution error description",
        "errors": {
            "field_name": ["Specific validation error message"]
        }
    }, indent=2))
    
    # --- MODULE 1: AUTHENTICATION & PIN SECURITY ---
    doc.add_page_break()
    h1 = doc.add_heading("2. Authentication & PIN Security APIs", level=1)
    h1.style.font.color.rgb = RGBColor(30, 58, 138)
    
    # 2.1 Universal Login
    h2 = doc.add_heading("2.1 Universal Employee Login", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Authenticates an employee using Email, Mobile, or Employee ID with either account password or security PIN. Issues a secure Laravel Sanctum Bearer Token.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/auth/login", "Public")
    
    doc.add_paragraph("Request Parameters:")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.4, 1.1, 1.0, 3.3], 
        ["Parameter", "Type", "Required", "Description"],
        [
            ["login", "string", "Yes", "Employee Email, Mobile number, or unique Employee ID"],
            ["password", "string", "Optional*", "Account password (*required if PIN not provided)"],
            ["pin", "string", "Optional*", "4 to 6-digit numeric PIN (*required if password not provided)"],
            ["device_name", "string", "No", "Client device identifier (e.g. 'Samsung Galaxy S24')"]
        ]
    )
    doc.add_paragraph("\nSample Success Response (200 OK):")
    add_code_block(doc, json.dumps({
        "success": True,
        "message": "Employee login successful.",
        "data": {
            "token": "1|nK0R2bL8uP3qXyZ...",
            "token_type": "Bearer",
            "employee": {
                "employee_id": 99,
                "name": "Pransi Gupta",
                "email": "pransi.g@neuralinfo.org",
                "mobile": "7668036640",
                "designation": "Software Developer",
                "is_pin_set": True,
                "is_pin_enabled": True,
                "pin_set_at": "2026-09-22T10:30:00+05:30",
                "last_login_at": "2026-09-22T10:31:00+05:30",
                "status": "Active"
            }
        }
    }, indent=2))

    # 2.2 Login with Mobile
    h2 = doc.add_heading("2.2 Login with Mobile Number", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Dedicated mobile authentication endpoint allowing login via mobile number with password or PIN.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/auth/login/mobile", "Public")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.4, 1.1, 1.0, 3.3], 
        ["Parameter", "Type", "Required", "Description"],
        [
            ["mobile", "string", "Yes", "Registered 10-digit mobile number"],
            ["password", "string", "Optional*", "Account password (*required if PIN is omitted)"],
            ["pin", "string", "Optional*", "4-6 digit numeric PIN (*required if password is omitted)"],
            ["device_name", "string", "No", "Device / Client Name"]
        ]
    )

    # 2.3 Login with EmpID
    h2 = doc.add_heading("2.3 Login with Employee ID", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Dedicated authentication endpoint allowing employees to login using their official Employee ID with password or PIN.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/auth/login/empid", "Public")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.4, 1.1, 1.0, 3.3], 
        ["Parameter", "Type", "Required", "Description"],
        [
            ["employee_id", "string/int", "Yes", "Official Employee ID"],
            ["password", "string", "Optional*", "Account password (*required if PIN is omitted)"],
            ["pin", "string", "Optional*", "4-6 digit numeric PIN (*required if password is omitted)"],
            ["device_name", "string", "No", "Device / Client Name"]
        ]
    )

    # 2.4 Quick PIN Login
    h2 = doc.add_heading("2.4 Quick PIN Login", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Fast biometric-style PIN login endpoint for Mobile App daily unlock.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/auth/login/pin", "Public")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.4, 1.1, 1.0, 3.3], 
        ["Parameter", "Type", "Required", "Description"],
        [
            ["identifier", "string", "Yes", "Mobile number, Email, or Employee ID"],
            ["pin", "string", "Yes", "4 to 6 digit security PIN"],
            ["device_name", "string", "No", "Device / Client Name"]
        ]
    )

    # 2.5 PIN Setup
    h2 = doc.add_heading("2.5 Security PIN Setup", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Allows an authenticated employee to create and activate a 4 to 6-digit numeric login PIN. If a PIN already exists, requires account password for security authorization.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/auth/pin/setup", "Bearer Token")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.4, 1.1, 1.0, 3.3], 
        ["Parameter", "Type", "Required", "Description"],
        [
            ["pin", "string", "Yes", "4 to 6 digit numeric security PIN"],
            ["pin_confirmation", "string", "Yes", "Must match pin"],
            ["password", "string", "Conditional", "Required if replacing an existing PIN"]
        ]
    )

    # 2.6 Change PIN
    h2 = doc.add_heading("2.6 Change Existing PIN", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Allows an employee to change their existing PIN after verifying their current PIN.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/auth/pin/change", "Bearer Token")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.5, 1.1, 1.0, 3.2], 
        ["Parameter", "Type", "Required", "Description"],
        [
            ["current_pin", "string", "Yes", "Existing 4-6 digit numeric PIN"],
            ["new_pin", "string", "Yes", "New 4-6 digit PIN (must be different from current)"],
            ["new_pin_confirmation", "string", "Yes", "Must match new_pin"]
        ]
    )

    # 2.7 Forgot PIN Request OTP
    h2 = doc.add_heading("2.7 Forgot PIN - Request OTP", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Initiates PIN recovery by dispatching a 6-digit OTP to the employee's registered mobile number (valid for 10 minutes).")
    add_endpoint_header(doc, "POST", "/api/v1/employee/auth/pin/forgot-request", "Public")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.4, 1.1, 1.0, 3.3], 
        ["Parameter", "Type", "Required", "Description"],
        [
            ["identifier", "string", "Yes", "Registered Mobile number, Email, or Employee ID"]
        ]
    )
    doc.add_paragraph("\nSample OTP Request Response:")
    add_code_block(doc, json.dumps({
        "success": True,
        "message": "A 6-digit OTP has been generated for PIN recovery. It will expire in 10 minutes.",
        "data": {
            "identifier": "7668036640",
            "masked_mobile": "******6640",
            "expires_in_minutes": 10,
            "otp_code": "856651"
        }
    }, indent=2))

    # 2.8 Reset PIN with OTP
    h2 = doc.add_heading("2.8 Reset PIN with OTP Verification", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Verifies the OTP code received during recovery and sets a new security PIN.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/auth/pin/reset", "Public")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.5, 1.1, 1.0, 3.2], 
        ["Parameter", "Type", "Required", "Description"],
        [
            ["identifier", "string", "Yes", "Mobile number, Email, or Employee ID"],
            ["otp", "string", "Yes", "6-digit OTP received via SMS"],
            ["new_pin", "string", "Yes", "New 4 to 6-digit numeric PIN"],
            ["new_pin_confirmation", "string", "Yes", "Must match new_pin"]
        ]
    )

    # 2.9 Enable / Disable PIN Login (Toggle)
    h2 = doc.add_heading("2.9 Enable / Disable PIN Login", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Allows the employee to toggle PIN login on or off after confirming their current PIN.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/auth/pin/toggle", "Bearer Token")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.4, 1.1, 1.0, 3.3], 
        ["Parameter", "Type", "Required", "Description"],
        [
            ["enable", "boolean", "Yes", "true to enable, false to disable PIN login"],
            ["pin", "string", "Yes", "Current security PIN for authorization"]
        ]
    )

    # 2.10 PIN Status
    h2 = doc.add_heading("2.10 Get PIN Status", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Checks whether the employee has configured a PIN and whether PIN authentication is currently enabled.")
    add_endpoint_header(doc, "GET", "/api/v1/employee/auth/pin/status", "Bearer Token")
    add_code_block(doc, json.dumps({
        "success": True,
        "data": {
            "employee_id": 99,
            "is_pin_set": True,
            "is_pin_enabled": True,
            "pin_set_at": "2026-09-22T10:30:00+05:30",
            "last_login_at": "2026-09-22T10:31:00+05:30"
        }
    }, indent=2))

    # 2.11 Security Activity Logs
    h2 = doc.add_heading("2.11 Security & Auth Activity Logs", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Returns the last 20 login, PIN change, and security events for audit trail and device tracking.")
    add_endpoint_header(doc, "GET", "/api/v1/employee/auth/activity-logs", "Bearer Token")

    # 2.12 Logout
    h2 = doc.add_heading("2.12 Employee Logout", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Invalidates and revokes the active Bearer session token.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/auth/logout", "Bearer Token")
    add_code_block(doc, json.dumps({"success": True, "message": "Logged out successfully. Token revoked."}, indent=2))
    
    # 2.13 Profile
    h2 = doc.add_heading("2.13 Get Employee Profile", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Fetches complete authenticated employee details with organizational hierarchy, agency, supervisor, and project assignments.")
    add_endpoint_header(doc, "GET", "/api/v1/employee/profile", "Bearer Token")
    doc.add_paragraph("Sample Profile Response:")
    add_code_block(doc, json.dumps({
        "success": True,
        "data": {
            "employee_id": 99,
            "name": "Pransi Gupta",
            "email": "pransi.g@neuralinfo.org",
            "mobile": "7668036640",
            "designation": {"id": 5, "name": "Software Developer"},
            "agency": {"id": 2, "agency_name": "Neural Info Overseas Private Limited"},
            "supervisor": {"id": 1, "name": "Sheetanshu Shrivastava"},
            "location": {"city": "Bhopal", "state": "Madhya Pradesh"},
            "assigned_projects": [{"project_id": 12, "project_name": "EMS Visitor Portal", "role": "Lead Developer"}]
        }
    }, indent=2))
    
    # 2.14 Agencies
    h2 = doc.add_heading("2.14 Get Agencies List", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Returns active staffing partner organizations.")
    add_endpoint_header(doc, "GET", "/api/v1/employee/agencies", "Bearer Token")
    
    # --- MODULE 2: ATTENDANCE ---
    doc.add_page_break()
    h1 = doc.add_heading("3. Attendance Management APIs", level=1)
    h1.style.font.color.rgb = RGBColor(30, 58, 138)
    
    # 3.1 Check-In
    h2 = doc.add_heading("3.1 Mark Check-In", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Logs attendance check-in for the current date with GPS coordinates, IP address, and client device info.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/attendance/check-in", "Bearer Token")
    
    doc.add_paragraph("Request Parameters:")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.4, 1.1, 1.0, 3.3],
        ["Parameter", "Type", "Required", "Description"],
        [
            ["latitude", "numeric", "No", "GPS Latitude (between -90 and 90)"],
            ["longitude", "numeric", "No", "GPS Longitude (between -180 and 180)"],
            ["note", "string", "No", "Check-in remarks or location info (Max 1000 chars)"],
            ["device_info", "string", "No", "Client hardware/OS info (e.g. 'OnePlus 11')"]
        ]
    )
    doc.add_paragraph("\nSample Success Response (200 OK):")
    add_code_block(doc, json.dumps({
        "success": True,
        "message": "Checked in successfully at 09:30 AM.",
        "data": {
            "id": 482,
            "employee_id": 101,
            "date": "2026-09-21",
            "check_in_time": "09:30 AM",
            "is_checked_in": True,
            "is_checked_out": False,
            "status": "checked_in",
            "working_minutes": 0,
            "check_in_location": {"latitude": 23.259933, "longitude": 77.412613, "ip": "192.168.1.45"}
        }
    }, indent=2))
    
    # 3.2 Check-Out
    h2 = doc.add_heading("3.2 Mark Check-Out", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Logs attendance check-out. Automatically calculates total worked minutes and formatted working hours.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/attendance/check-out", "Bearer Token")
    
    doc.add_paragraph("Request Parameters: Same as check-in (latitude, longitude, note, device_info).")
    doc.add_paragraph("Sample Success Response (200 OK):")
    add_code_block(doc, json.dumps({
        "success": True,
        "message": "Checked out successfully at 06:35 PM.",
        "data": {
            "id": 482,
            "date": "2026-09-21",
            "check_in_time": "09:30 AM",
            "check_out_time": "06:35 PM",
            "is_checked_in": True,
            "is_checked_out": True,
            "status": "checked_out",
            "working_minutes": 545,
            "working_hours_formatted": "9h 5m"
        }
    }, indent=2))
    
    # 3.3 Today Attendance
    h2 = doc.add_heading("3.3 Get Today's Live Attendance Status", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Returns live attendance status for today ('not_marked', 'checked_in', 'checked_out') to control mobile UI action buttons.")
    add_endpoint_header(doc, "GET", "/api/v1/employee/attendance/today", "Bearer Token")
    
    # 3.4 Attendance History
    h2 = doc.add_heading("3.4 Attendance History & Range Summary", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Retrieves historical attendance records with date range filters, pagination, and monthly total worked hours summary.")
    add_endpoint_header(doc, "GET", "/api/v1/employee/attendance/history?start_date=2026-09-01&end_date=2026-09-21", "Bearer Token")
    
    doc.add_paragraph("Query Parameters:")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.4, 1.1, 1.0, 3.3],
        ["Parameter", "Type", "Required", "Description"],
        [
            ["start_date", "date (YYYY-MM-DD)", "No", "Range start date"],
            ["end_date", "date (YYYY-MM-DD)", "No", "Range end date"],
            ["status", "string", "No", "Filter: checked_in, checked_out, present, absent"],
            ["per_page", "integer", "No", "Items per page (Default 20)"]
        ]
    )
    
    # --- MODULE 3: CALENDAR ---
    doc.add_page_break()
    h1 = doc.add_heading("4. Calendar & Schedule APIs", level=1)
    h1.style.font.color.rgb = RGBColor(30, 58, 138)
    
    # 4.1 Monthly Calendar
    h2 = doc.add_heading("4.1 Monthly Aggregated Calendar", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Aggregates employee attendance, weekly-offs (Sundays), public holidays, and approved leaves for any requested month into a single colored schedule payload.")
    add_endpoint_header(doc, "GET", "/api/v1/employee/calendar?month=9&year=2026", "Bearer Token")
    
    doc.add_paragraph("Sample Calendar Response:")
    add_code_block(doc, json.dumps({
        "success": True,
        "data": {
            "month": 9,
            "month_name": "September",
            "year": 2026,
            "summary": {
                "total_days": 30,
                "present_days": 18,
                "absent_days": 2,
                "leave_days": 1,
                "holiday_days": 1,
                "weekly_offs": 4
            },
            "days": [
                {
                    "date": "2026-09-01",
                    "day": 1,
                    "day_name": "Tuesday",
                    "day_type": "present",
                    "color": "#198754",
                    "attendance": {"check_in": "09:30 AM", "check_out": "06:30 PM", "working_minutes": 540}
                },
                {
                    "date": "2026-09-06",
                    "day": 6,
                    "day_name": "Sunday",
                    "day_type": "weekly_off",
                    "color": "#ffc107"
                }
            ]
        }
    }, indent=2))
    
    # 4.2 Date Details
    h2 = doc.add_heading("4.2 Single Date Activity Breakdown", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Provides in-depth activity logs for a tapped date (exact timestamps, IP, location, leave reasons, or holiday names).")
    add_endpoint_header(doc, "GET", "/api/v1/employee/calendar/date-details?date=2026-09-21", "Bearer Token")
    
    # --- MODULE 4: LEAVE MANAGEMENT ---
    doc.add_page_break()
    h1 = doc.add_heading("5. Leave Management APIs", level=1)
    h1.style.font.color.rgb = RGBColor(30, 58, 138)
    
    # 5.1 Leave Types
    h2 = doc.add_heading("5.1 Get Configured Leave Types", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Returns company leave categories (CL, SL, EL) and quota allowances for dropdown selections.")
    add_endpoint_header(doc, "GET", "/api/v1/employee/leave/types", "Bearer Token")
    
    # 5.2 Apply Leave
    h2 = doc.add_heading("5.2 Apply for Leave", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Submits a leave application with date selection, reason, document attachment, and automated overlap validation.")
    add_endpoint_header(doc, "POST", "/api/v1/employee/leave/apply", "Bearer Token (multipart/form-data)")
    
    doc.add_paragraph("Request Parameters:")
    tbl = doc.add_table(rows=1, cols=4)
    format_table(tbl, [1.4, 1.1, 1.0, 3.3],
        ["Parameter", "Type", "Required", "Description"],
        [
            ["leave_type_id", "integer", "Yes", "ID from leave types API"],
            ["from_date", "date", "Yes", "Leave starting date (YYYY-MM-DD)"],
            ["to_date", "date", "Yes", "Leave ending date (>= from_date)"],
            ["reason", "string", "Yes", "Detailed justification (Max 2000 chars)"],
            ["document", "file", "No", "Supporting attachment (PDF, PNG, JPG, Max 5MB)"]
        ]
    )
    
    # 5.3 Leave List
    h2 = doc.add_heading("5.3 List Leave Applications", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Fetches employee's submitted leaves with status filters (ALL, SENT, APPROVED, REJECTED) and year filter.")
    add_endpoint_header(doc, "GET", "/api/v1/employee/leave/list?status=ALL&year=2026", "Bearer Token")
    
    # 5.4 Leave Details
    h2 = doc.add_heading("5.4 Get Leave Details & Approval Timeline", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Returns single leave record with manager action timestamps and activity log.")
    add_endpoint_header(doc, "GET", "/api/v1/employee/leave/{id}", "Bearer Token")
    
    # --- MODULE 5: HOLIDAYS ---
    doc.add_page_break()
    h1 = doc.add_heading("6. Company Holiday APIs", level=1)
    h1.style.font.color.rgb = RGBColor(30, 58, 138)
    
    h2 = doc.add_heading("6.1 Annual Public Holiday List", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Retrieves all declared public/festival holidays for the year with 'is_past' flags.")
    add_endpoint_header(doc, "GET", "/api/v1/holidays?year=2026", "Bearer Token")
    
    h2 = doc.add_heading("6.2 Monthly Grouped Holiday Calendar", level=2)
    h2.style.font.color.rgb = RGBColor(2, 132, 199)
    doc.add_paragraph("Returns holidays grouped by month name (January, August, etc.) for direct calendar UI rendering.")
    add_endpoint_header(doc, "GET", "/api/v1/holidays/calendar?year=2026", "Bearer Token")
    
    # --- MODULE 6: DEVELOPER TOOLS ---
    doc.add_page_break()
    h1 = doc.add_heading("7. Integration & Postman Tools", level=1)
    h1.style.font.color.rgb = RGBColor(30, 58, 138)
    
    doc.add_paragraph("Developers can immediately test and integrate using these ready-to-use resources:")
    doc.add_paragraph("1. Interactive Web API Documentation:").bold = True
    doc.add_paragraph("Access in browser at: http://localhost/ems-visitor/public/api-documentation (or /api-docs)")
    
    doc.add_paragraph("2. 1-Click Postman Collection Download:").bold = True
    doc.add_paragraph("Direct Download Link: http://localhost/ems-visitor/public/api-documentation/postman.json\nImport this JSON into Postman to instantly test all 16 endpoints with pre-configured headers, parameters, and environment variables.")
    
    doc.save(output_path)
    print(f"Document successfully created at: {output_path}")

if __name__ == "__main__":
    out_file = os.path.abspath("c:/xampp/htdocs/ems-visitor/EMS_API_Documentation.docx")
    build_word_doc(out_file)
