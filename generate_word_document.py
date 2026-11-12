import os
import sys
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=120, bottom=120, left=160, right=160):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'''<w:tcMar {nsdecls("w")}>
        <w:top w:w="{top}" w:type="dxa"/>
        <w:bottom w:w="{bottom}" w:type="dxa"/>
        <w:left w:w="{left}" w:type="dxa"/>
        <w:right w:w="{right}" w:type="dxa"/>
    </w:tcMar>''')
    tcPr.append(tcMar)

def set_cell_borders(cell, top=None, bottom=None, left=None, right=None):
    tcPr = cell._tc.get_or_add_tcPr()
    borders = ['<w:tcBorders ' + nsdecls("w") + '>']
    for side, border in [('top', top), ('bottom', bottom), ('left', left), ('right', right)]:
        if border:
            borders.append(f'<w:{side} w:val="{border.get("val", "single")}" w:sz="{border.get("sz", 4)}" w:space="0" w:color="{border.get("color", "E2E8F0")}"/>')
        else:
            borders.append(f'<w:{side} w:val="none"/>')
    borders.append('</w:tcBorders>')
    tcPr.append(parse_xml(''.join(borders)))

def add_callout(doc, title, text, box_type="info"):
    colors = {
        "info": {"bg": "EFF6FF", "border": "2563EB", "title_col": (30, 64, 175)},
        "success": {"bg": "ECFDF5", "border": "059669", "title_col": (4, 120, 87)},
        "warning": {"bg": "FFFBEB", "border": "D97706", "title_col": (180, 83, 9)},
        "hint": {"bg": "F8FAFC", "border": "475569", "title_col": (30, 41, 59)}
    }
    cfg = colors.get(box_type, colors["info"])

    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl.autofit = False
    cell = tbl.cell(0, 0)
    cell.width = Inches(6.8)
    
    set_cell_background(cell, cfg["bg"])
    set_cell_margins(cell, top=140, bottom=140, left=180, right=180)
    set_cell_borders(cell, left={"val": "single", "sz": 24, "color": cfg["border"]},
                           top={"val": "single", "sz": 4, "color": "E2E8F0"},
                           bottom={"val": "single", "sz": 4, "color": "E2E8F0"},
                           right={"val": "single", "sz": 4, "color": "E2E8F0"})

    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(3)
    p.paragraph_format.line_spacing = 1.15
    run_t = p.add_run(f"📌 {title}\n")
    run_t.bold = True
    run_t.font.name = "Segoe UI"
    run_t.font.size = Pt(10.5)
    run_t.font.color.rgb = RGBColor(*cfg["title_col"])

    run_b = p.add_run(text)
    run_b.font.name = "Segoe UI"
    run_b.font.size = Pt(9.5)
    run_b.font.color.rgb = RGBColor(51, 65, 85)

    doc.add_paragraph()

def format_table(tbl, col_widths, headers, rows):
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl.autofit = False
    
    hdr_cells = tbl.rows[0].cells
    for i, title in enumerate(headers):
        hdr_cells[i].width = Inches(col_widths[i])
        set_cell_background(hdr_cells[i], "0F172A")
        set_cell_margins(hdr_cells[i], top=120, bottom=120, left=140, right=140)
        p = hdr_cells[i].paragraphs[0]
        p.paragraph_format.space_before = Pt(2)
        p.paragraph_format.space_after = Pt(2)
        run = p.add_run(title)
        run.bold = True
        run.font.name = "Segoe UI"
        run.font.size = Pt(9.5)
        run.font.color.rgb = RGBColor(255, 255, 255)
    
    for r_idx, row_data in enumerate(rows):
        row_cells = tbl.add_row().cells
        bg_color = "F8FAFC" if r_idx % 2 == 1 else "FFFFFF"
        for c_idx, val in enumerate(row_data):
            row_cells[c_idx].width = Inches(col_widths[c_idx])
            set_cell_background(row_cells[c_idx], bg_color)
            set_cell_margins(row_cells[c_idx], top=90, bottom=90, left=140, right=140)
            p = row_cells[c_idx].paragraphs[0]
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            p.paragraph_format.line_spacing = 1.15
            run = p.add_run(str(val))
            run.font.name = "Segoe UI"
            run.font.size = Pt(9)
            if c_idx == 0:
                run.bold = True
                run.font.color.rgb = RGBColor(30, 58, 138)
            else:
                run.font.color.rgb = RGBColor(51, 65, 85)

def add_heading_1(doc, text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(18)
    h.paragraph_format.space_after = Pt(6)
    h.paragraph_format.keep_with_next = True
    run = h.add_run(text)
    run.font.name = "Segoe UI"
    run.font.size = Pt(16)
    run.bold = True
    run.font.color.rgb = RGBColor(15, 23, 42)
    pBrd = parse_xml(f'<w:pBrd {nsdecls("w")}><w:bottom w:val="single" w:sz="16" w:space="4" w:color="0284C7"/></w:pBrd>')
    h._p.get_or_add_pPr().append(pBrd)
    return h

def add_heading_2(doc, text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(13)
    h.paragraph_format.space_after = Pt(4)
    h.paragraph_format.keep_with_next = True
    run = h.add_run(text)
    run.font.name = "Segoe UI"
    run.font.size = Pt(13)
    run.bold = True
    run.font.color.rgb = RGBColor(2, 132, 199)
    return h

def add_para(doc, text, bold_prefix="", italic=False):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(4)
    p.paragraph_format.line_spacing = 1.15
    if bold_prefix:
        r_pre = p.add_run(bold_prefix)
        r_pre.bold = True
        r_pre.font.name = "Segoe UI"
        r_pre.font.size = Pt(10)
        r_pre.font.color.rgb = RGBColor(15, 23, 42)
    run = p.add_run(text)
    run.font.name = "Segoe UI"
    run.font.size = Pt(10)
    run.italic = italic
    run.font.color.rgb = RGBColor(51, 65, 85)
    return p

def add_image_safely(doc, image_path, width_inches=6.2, caption=""):
    if os.path.exists(image_path):
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.space_before = Pt(8)
        p.paragraph_format.space_after = Pt(2)
        run = p.add_run()
        run.add_picture(image_path, width=Inches(width_inches))
        if caption:
            p_cap = doc.add_paragraph()
            p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p_cap.paragraph_format.space_before = Pt(1)
            p_cap.paragraph_format.space_after = Pt(8)
            r_cap = p_cap.add_run(caption)
            r_cap.font.name = "Segoe UI"
            r_cap.font.size = Pt(8.5)
            r_cap.italic = True
            r_cap.font.color.rgb = RGBColor(100, 116, 139)

def generate_ems_full_doc():
    doc = Document()

    for section in doc.sections:
        section.top_margin = Inches(0.8)
        section.bottom_margin = Inches(0.8)
        section.left_margin = Inches(0.8)
        section.right_margin = Inches(0.8)

    # Header / Logo
    logo_path = "public/frontent/assets/img/icons/logo_1.png"
    if os.path.exists(logo_path):
        add_image_safely(doc, logo_path, width_inches=2.2)

    title_p = doc.add_paragraph()
    title_p.paragraph_format.space_before = Pt(10)
    title_p.paragraph_format.space_after = Pt(4)
    run_title = title_p.add_run("EMPLOYEE MANAGEMENT & PAYROLL PROCESS SYSTEM (EMS)")
    run_title.bold = True
    run_title.font.name = "Segoe UI"
    run_title.font.size = Pt(21)
    run_title.font.color.rgb = RGBColor(15, 23, 42)

    sub_p = doc.add_paragraph()
    sub_p.paragraph_format.space_before = Pt(0)
    sub_p.paragraph_format.space_after = Pt(12)
    run_sub = sub_p.add_run("Comprehensive Operational Workflow, User Roles Hierarchy, Leave Management Lifecycle & Payroll Processing Engine\n(Kaise Kaam Kar Raha Hai, Kha Se Kya Ho Raha Hai, Kis Button Se Kya Action Perform Hoti Hai)")
    run_sub.font.name = "Segoe UI"
    run_sub.font.size = Pt(11)
    run_sub.italic = True
    run_sub.font.color.rgb = RGBColor(2, 132, 199)

    # Metadata Table
    meta_tbl = doc.add_table(rows=1, cols=4)
    format_table(meta_tbl, [1.7, 1.7, 1.7, 1.7], 
                 ["Project Platform", "Technology Stack", "Organization", "Deployment Target"],
                 [["Chitragupta EMS (v2.0)", "Laravel 11, PHP 8.2+, MySQL", "Neural Info Solutions Pvt. Ltd.", "Local XAMPP / Live Cloud"]])
    
    doc.add_paragraph()

    add_callout(doc, "DOCUMENT SCOPE (Khas Taur Par Employee Management, Leaves Aur Payroll Ke Liye)", 
                "Yeh document EMS (Employee Management System) ke 3 mukhya pillars ko comprehensively cover karta hai: (1) Employee Profile & Document Management, (2) Attendance & Multi-Level Leave Workflow, aur (3) Automated Attendance-Linked Payroll Processing Engine (Salary Structure, Batch Generation, LWP Calculation, Disbursement, A4 PDF Payslips, aur Annual CTC Increments).", "info")

    # -------------------------------------------------------------
    # 1. USER ROLES & ACCESS HIERARCHY
    # -------------------------------------------------------------
    add_heading_1(doc, "1. User Roles & Access Matrix (System Me Kitne Users / Roles Hain?)")
    
    add_para(doc, "System me Laravel ke multi-guard architecture (`web` aur `employee`) ke under kul 5 mukhya roles hain jo administration, team management, aur employee operations ko handle karte hain:")

    add_image_safely(doc, "public/frontent/assets/img/graphics/ems_user_roles.png", width_inches=6.4, 
                     caption="Figure 1.1: EMS 5-Tier User Roles & Security Access Matrix")

    roles_tbl = doc.add_table(rows=1, cols=4)
    format_table(roles_tbl, [1.4, 1.3, 1.7, 2.4],
                 ["Role Name", "Auth Guard & ID", "Portal URL", "Key Responsibilities & Permissions"],
                 [
                     ["1. Super Admin", "web (role_id: 1)", "/home", "Poore system par 100% control, Spatie permissions & roles assign karna, Users create/delete karna, Master audit."],
                     ["2. System Admin", "web (role_id: 2)", "/home", "Daily operations: Monthly Payroll Batches generate & approve karna, Agency & Branch masters, Shift timings, Salary structures setup."],
                     ["3. HR Manager", "web (role_id: 3)", "/home", "Staff onboarding, Document Vault me Aadhaar/PAN verify karna, Leave requests approve/reject karna, Monthly attendance registers export."],
                     ["4. Supervisor", "web (role_id: 6)", "/home", "Apne subordinate team members ki daily punch logs dekhna, team task monitoring, aur team leave requests recommend karna."],
                     ["5. Employee", "employee (Guard)", "/employee/dashboard", "Self-service portal: GPS coordinates se Punch In/Out, Leave quota dekhna, Leave apply karna, Monthly Payslip PDF download."]
                 ])
    doc.add_paragraph()

    # -------------------------------------------------------------
    # 2. EMPLOYEE MANAGEMENT MODULE
    # -------------------------------------------------------------
    add_heading_1(doc, "2. Employee Management Module (Bio-Data, Master Setup & Document Vault)")
    
    add_para(doc, "Employee Management module me staff directory, organization structure, aur verification shamil hain:", bold_prefix="Module Overview: ")
    add_para(doc, "Employee ka 7-tab registration form (Personal details, Emergency contact, Bank details with IFSC, Salary package, Agency mapping, Branch & Shift assignment).", bold_prefix="1. Employee Profile Setup (/employees/create): ")
    add_para(doc, "Single click me bulk Excel/CSV file upload karke hazaron employees ko system me import karne ki suvidha.", bold_prefix="2. Bulk Excel Import (/employees/import): ")
    add_para(doc, "Employee ke upload kiye gaye Aadhaar Card, PAN Card, Graduation/Post-Graduation degrees aur bank passbooks ka audit status (Verified / Rejected) stamp karna.", bold_prefix="3. Document Vault (/employees/documents): ")

    emp_btns_tbl = doc.add_table(rows=1, cols=3)
    format_table(emp_btns_tbl, [2.2, 1.8, 2.8],
                 ["Button / Action Name", "Screen Location", "What Happens on Click (Button Dabane Par Kya Hota Hai?)"],
                 [
                     ["[ ➕ Add Employee ]", "/employees header", "Multi-tab registration form open karta hai jisme basic info, posting, bank account aur salary map hoti hai."],
                     ["[ 📥 Import Excel ]", "/employees header", "Modal popup open karta hai. Validated CSV/Excel file upload hone par instant bulk insert karta hai."],
                     ["[ 📄 Download Sample ]", "Import modal", "Correct formatting ke sath sample spreadsheet template download karta hai."],
                     ["[ Status Toggle Switch ]", "Action column", "1-click me Active (1) se Inactive (0) karta hai. Inactive hote hi employee ka login instantly block ho jata hai."],
                     ["[ 🛡️ Verify Document Status ]", "Document Vault table", "Aadhaar/PAN ko 'Verified' mark karta hai aur verification timestamp & HR user ID save karta hai."]
                 ])
    doc.add_paragraph()

    # -------------------------------------------------------------
    # 3. ATTENDANCE & LEAVE MANAGEMENT
    # -------------------------------------------------------------
    add_heading_1(doc, "3. Attendance & Leave Management Workflow (Leaves Kaise Kaam Karti Hain?)")
    
    add_para(doc, "Attendance aur Leaves ka complete lifecycle payroll calculation se directly connected hota hai. Niche diye gaye workflow graphic me iska step-by-step pipeline dikhaya gaya hai:")

    add_image_safely(doc, "public/frontent/assets/img/graphics/leave_management_workflow.png", width_inches=6.4, 
                     caption="Figure 3.1: Complete Leave Management Lifecycle from Allocation to Payroll LWP Sync")

    add_heading_2(doc, "Step-by-Step Leave Workflow (Kadam-Ba-Kadam Flow):")
    add_para(doc, "Admin designation-wise leave quota map karta hai (e.g. 12 Casual Leaves, 10 Sick Leaves, 15 Privilege Leaves per year).", bold_prefix="Kadam 1 (Leave Allotment): ")
    add_para(doc, "Employee apne portal (/employee/leaves) par jata hai. Screen par Total Allotted, Used, aur Remaining leaves live dikhti hain. [Apply New Leave] button dabakar Leave Type, Dates, aur Reason enter karta hai.", bold_prefix="Kadam 2 (Employee Application): ")
    add_para(doc, "System check karta hai ki employee ke quota me remaining days bache hain ya nahi. Agar balance khatam ho chuka ho, toh system warn karta hai aur request Loss of Pay (LWP) me mark ho jaati hai.", bold_prefix="Kadam 3 (Balance & Quota Validation): ")
    add_para(doc, "Immediate Reporting Manager/Supervisor ko notification jata hai. Supervisor dates aur project dependency check karke recommend karta hai.", bold_prefix="Kadam 4 (Supervisor Review): ")
    add_para(doc, "HR Manager ke portal (/attendance/leave/request) par pending leave aati hai. HR [Approve] ya [Reject] button dabata hai. Approve hone par leave balance auto-deduct ho jata hai aur employee ko approval alert milta hai.", bold_prefix="Kadam 5 (HR Final Approval): ")
    add_para(doc, "Approved leaves monthly Attendance Register me 'L' (Leave) mark ho jaati hain. Yeh din paid days count hote hain.", bold_prefix="Kadam 6 (Attendance Register Sync): ")
    add_para(doc, "Agar employee unapproved absent rehta hai, toh system un dino ko LWP (Loss of Pay) count karta hai aur month-end payroll me salary se deduct karta hai.", bold_prefix="Kadam 7 (Payroll LWP Impact): ")

    leave_btns_tbl = doc.add_table(rows=1, cols=3)
    format_table(leave_btns_tbl, [2.2, 1.8, 2.8],
                 ["Button Name", "Screen Location", "What Happens on Click (Button Dabane Par Kya Hota Hai?)"],
                 [
                     ["[ 📍 Punch In / Check-In ]", "Employee Dashboard", "Browser/Device se GPS Latitude, Longitude, IP address aur time uthakar daily attendance in_time stamp karta hai."],
                     ["[ 📍 Punch Out / Check-Out ]", "Employee Dashboard", "Current time par out_time stamp karta hai aur total daily working hours calculate karta hai."],
                     ["[ Apply New Leave ]", "/employee/leaves", "Leave application modal open karta hai (Leave Type select, From/To dates, Reason, aur medical document upload)."],
                     ["[ ✅ Approve Leave ]", "/attendance/leave/request", "Leave status ko 'APPROVED' mark karta hai, balance deduct karta hai aur employee ko email bhejta hai."],
                     ["[ ❌ Reject Leave ]", "/attendance/leave/request", "Rejection remarks ke sath request decline karta hai aur status 'REJECTED' ho jata hai."],
                     ["[ Export Register (Excel) ]", "/attendance/register", "Poore mahine ka Day 1 se Day 31 tak ka P/A/L/WO/H matrix Excel file me generate karke download karta hai."]
                 ])
    doc.add_paragraph()

    # -------------------------------------------------------------
    # 4. PAYROLL PROCESSING ENGINE
    # -------------------------------------------------------------
    add_heading_1(doc, "4. Payroll Processing Engine (Salary Kaise Generate Aur Disburse Hoti Hai?)")
    
    add_para(doc, "EMS Payroll Engine attendance aur approved leaves ke data ko real-time auto-calculate karke monthly salary batches, tax deductions, bank disbursement files aur A4 PDF payslips banata hai:")

    add_image_safely(doc, "public/frontent/assets/img/graphics/payroll_lifecycle_flowchart.png", width_inches=6.4, 
                     caption="Figure 4.1: End-to-End Payroll Processing Pipeline & Calculation Engine")

    add_heading_2(doc, "4.1 Salary Structure & Components Breakdown:")
    add_para(doc, "Admin jab kisi employee ki Monthly Gross Salary enter karta hai (/masters/payroll/salary-structure/create), toh system niche diye gaye formula ke mutabik earnings aur deductions real-time split karta hai:")

    add_image_safely(doc, "public/frontent/assets/img/graphics/salary_structure_mockup.png", width_inches=6.4, 
                     caption="Figure 4.2: Real-time Salary Structure Matrix (Earnings vs Statutory Deductions)")

    salary_comp_tbl = doc.add_table(rows=1, cols=3)
    format_table(salary_comp_tbl, [2.0, 2.0, 2.8],
                 ["Component Name", "Category", "Calculation Formula / Rule"],
                 [
                     ["Basic Salary", "Earning", "50% of Monthly Gross (e.g. ₹25,000 on ₹50,000 Gross)"],
                     ["House Rent Allowance (HRA)", "Earning", "25% of Monthly Gross (e.g. ₹12,500 on ₹50,000 Gross)"],
                     ["Conveyance Allowance", "Earning", "Fixed standard allowance (₹2,000 per month)"],
                     ["Medical Allowance", "Earning", "Fixed standard allowance (₹1,500 per month)"],
                     ["Special Allowance", "Earning", "Remaining balance ensuring total earnings match 100% Gross"],
                     ["PF Employee Contribution", "Deduction", "12% of Basic Salary (Statutory maximum cap ₹1,800/mo)"],
                     ["ESI Employee Contribution", "Deduction", "0.75% of Gross (Applicable only if Gross <= ₹21,000/mo)"],
                     ["Professional Tax (PT)", "Deduction", "State government slab governed (e.g. ₹200 per month)"],
                     ["Monthly TDS", "Deduction", "Income tax projected slab based on annual taxable CTC"],
                     ["Loss of Pay (LWP)", "Attendance Deduction", "(Monthly Gross / Total Month Days) x LWP Days"]
                 ])
    doc.add_paragraph()

    add_heading_2(doc, "4.2 Monthly Payroll Processing Lifecycle (Kadam-Ba-Kadam):")

    add_para(doc, "Month-end par system attendance register me daily punch logs aur approved leaves ko lock karta hai.", bold_prefix="Stage 1 (Cut-Off): ")
    add_para(doc, "Total Calendar Days (30/31), Working Days, Attended Days (Present + Approved Leave + Holidays), aur LWP Days calculate hote hain. Payable Days = Calendar Days - LWP Days.", bold_prefix="Stage 2 (LWP Calculation): ")
    add_para(doc, "Admin /payroll/process par jata hai, Month aur Year select karke [Generate Payroll] button dabata hai. System sabhi active employees ke liye PayrollBatch create karta hai.", bold_prefix="Stage 3 (Batch Generation): ")
    add_para(doc, "Admin /payroll/batches par sabhi employees ki Gross, Deductions aur Net in-hand amount verify karta hai aur [Approve Batch] dabata hai.", bold_prefix="Stage 4 (Batch Approval): ")
    add_para(doc, "Admin bank transfer ke baad [Disburse Selected] dabata hai aur UTR / Payment reference number save karta hai. Status 'Paid' ho jata hai.", bold_prefix="Stage 5 (Disbursement): ")
    add_para(doc, "Employees ke portal par monthly payslip release ho jaati hai. Individual A4 formatted PDF Payslip (jisme Indian Currency words me net amount hota hai) download ki ja sakti hai.", bold_prefix="Stage 6 (PDF Payslip Generation): ")

    payroll_btns_tbl = doc.add_table(rows=1, cols=3)
    format_table(payroll_btns_tbl, [2.2, 1.8, 2.8],
                 ["Button Name", "Screen Location", "What Happens on Click (Button Dabane Par Kya Hota Hai?)"],
                 [
                     ["[ Create Batch ]", "/payroll/process", "Month, Year, Branch aur Agency select karke payroll calculation initiate karta hai."],
                     ["[ Generate Payroll ]", "Process screen", "Attendance register se working days uthakar Basic, HRA, PF, ESI, TDS calculate karta hai."],
                     ["[ Approve Batch ]", "/payroll/batches", "Draft salary batch ko lock karta hai taaki calculations freeze ho sakein."],
                     ["[ Disburse Selected ]", "Batch details", "Payment mode (NEFT/Bank Transfer) ke sath status 'Disbursed' mark karta hai aur payslips release karta hai."],
                     ["[ Download Payslip PDF ]", "/payroll/payslips", "Individual employee ka official salary slip PDF watermark ke sath download karta hai."],
                     ["[ ➕ Add Increment ]", "/payroll/increments", "Employee ka existing CTC dikhata hai, revised CTC calculate karta hai, aur revised designation map karta hai."],
                     ["[ Download Increment Letter ]", "Increments history", "Official Company Increment / Appraisal Letter PDF format me generate karta hai."]
                 ])
    doc.add_paragraph()

    # -------------------------------------------------------------
    # 5. DATABASE SCHEMA
    # -------------------------------------------------------------
    add_heading_1(doc, "5. Database Schema & Key Tables (Backend Data Flow)")

    db_tbl = doc.add_table(rows=1, cols=3)
    format_table(db_tbl, [1.8, 2.2, 2.8],
                 ["Table Name", "Primary / Foreign Keys", "Business Data Stored (Kya Data Save Hota Hai)"],
                 [
                     ["users", "id, name, email, password, role_id, status", "Admin, HR, Super Admin users ke web login credentials."],
                     ["employees", "id, employee_name, employee_code, email, mobile, dept_id, designation_id, agency_id", "Staff master bio-data, designation, branch posting aur service record."],
                     ["employee_attendances", "id, employee_id, punch_date, in_time, out_time, latitude, longitude, ip_address", "Daily punch logs, GPS location coordinates aur biometric tracking data."],
                     ["leaves", "id, employee_id, leave_type_id, start_date, end_date, total_days, reason, status", "Leave applications, approval history aur supervisor comments."],
                     ["leave_mappings", "id, designation_id, leave_type_id, allow_days", "Designation-wise annual quota mapping (CL, SL, PL allowed days)."],
                     ["salary_structures", "id, employee_id, gross_salary, basic, hra, conveyance, pf, esi, ctc, status", "Active salary package, earning heads aur statutory deductions."],
                     ["payroll_batches", "id, batch_month, batch_year, agency_id, total_gross, total_net, status", "Monthly salary batch calculations (Draft, Approved, Disbursed)."],
                     ["payslips", "id, payroll_batch_id, employee_id, payable_days, lwp_days, net_salary, pdf_path", "Generated monthly payslips aur payment reference logs."],
                     ["employee_increments", "id, employee_id, previous_ctc, revised_ctc, increment_percentage, effective_date", "Annual CTC appraisal history aur revised salary structures."]
                 ])
    doc.add_paragraph()

    add_callout(doc, "SUMMARY & COMPLIANCE NOTE", 
                "Yeh EMS, Leave aur Payroll system 100% statutory compliant hai (Provident Fund, ESIC, Professional Tax, TDS). Attendance aur Leaves ke sath real-time integration ki wajah se manual calculations ki galtiyan 0% ho jaati hain.", "success")

    output_path1 = "EMS_Employee_Leave_Payroll_Complete_Workflow.docx"
    output_path2 = "public/EMS_Employee_Leave_Payroll_Complete_Workflow.docx"
    output_path3 = "EMS_Visitor_Complete_Project_Workflow.docx"
    output_path4 = "public/EMS_Visitor_Complete_Project_Workflow.docx"

    doc.save(output_path1)
    doc.save(output_path2)
    doc.save(output_path3)
    doc.save(output_path4)
    print("All Word documents successfully generated and saved!")

if __name__ == "__main__":
    generate_ems_full_doc()
