import os
from PIL import Image, ImageDraw, ImageFont

os.makedirs("public/frontent/assets/img/graphics", exist_ok=True)
os.makedirs("documents/graphics", exist_ok=True)

font_regular = "C:/Windows/Fonts/segoeui.ttf"
font_bold = "C:/Windows/Fonts/segoeuib.ttf"

def get_font(bold=False, size=16):
    try:
        path = font_bold if bold else font_regular
        return ImageFont.truetype(path, size)
    except:
        return ImageFont.load_default()

# -------------------------------------------------------------
# 1. LEAVE MANAGEMENT LIFECYCLE WORKFLOW
# -------------------------------------------------------------
def create_leave_workflow():
    w, h = 1000, 520
    img = Image.new("RGB", (w, h), "#0F172A")
    draw = ImageDraw.Draw(img)

    draw.text((40, 22), "EMS LEAVE MANAGEMENT - COMPLETE OPERATIONAL PIPELINE", fill="#38BDF8", font=get_font(True, 22))
    draw.text((40, 55), "From Leave Allocation & Employee Application to Supervisor Review, HR Approval & Payroll LWP Sync", fill="#94A3B8", font=get_font(False, 13))

    steps = [
        ("1. LEAVE QUOTA", "Designation based quota.\nCL, SL, PL & Holidays\nmapped in settings.", "#3B82F6", "#1E3A8A"),
        ("2. APPLY LEAVE", "Employee clicks [Apply].\nSelects Leave Type, Dates,\nReason & Certificate.", "#8B5CF6", "#4C1D95"),
        ("3. QUOTA CHECK", "System validates balance.\nRemaining = Allotted - Used.\nThrows error if limit exceeded.", "#EC4899", "#831843"),
        ("4. SUPERVISOR", "Team Lead gets email/push.\nReviews dates & workload.\nRecommends to HR.", "#F59E0B", "#78350F"),
        ("5. HR APPROVAL", "HR clicks [Approve] or\n[Reject] with remarks.\nBalance auto-deducted.", "#10B981", "#064E3B"),
        ("6. REGISTER SYNC", "Attendance register marks\nstatus as 'L' (Leave).\nPreserves paid day status.", "#06B6D4", "#164E63"),
        ("7. PAYROLL LWP", "Approved leave = Paid Day.\nUnapproved/Absent = LWP.\nDeducted from monthly salary.", "#6366F1", "#312E81"),
    ]

    card_w, card_h = 210, 150
    gap = 26
    start_x, start_y = 40, 110

    for i in range(4):
        x = start_x + i * (card_w + gap)
        y = start_y
        name, desc, accent, bg = steps[i]
        draw.rounded_rectangle([x, y, x + card_w, y + card_h], radius=10, fill="#1E293B", outline=accent, width=2)
        draw.rounded_rectangle([x, y, x + card_w, y + 36], radius=8, fill=bg)
        draw.text((x + 12, y + 8), name, fill="#FFFFFF", font=get_font(True, 13))
        draw.text((x + 12, y + 48), desc, fill="#CBD5E1", font=get_font(False, 11), spacing=4)

        if i < 3:
            arrow_x = x + card_w + 5
            arrow_y = y + card_h // 2
            draw.line([arrow_x, arrow_y, arrow_x + 16, arrow_y], fill="#64748B", width=3)
            draw.polygon([(arrow_x + 16, arrow_y - 5), (arrow_x + 22, arrow_y), (arrow_x + 16, arrow_y + 5)], fill="#64748B")

    # Down arrow
    down_x = start_x + 3 * (card_w + gap) + card_w // 2
    draw.line([down_x, start_y + card_h + 6, down_x, start_y + card_h + 36], fill="#64748B", width=3)
    draw.polygon([(down_x - 5, start_y + card_h + 36), (down_x, start_y + card_h + 42), (down_x + 5, start_y + card_h + 36)], fill="#64748B")

    # Row 2 (Steps 5, 6, 7)
    row2_y = start_y + card_h + 48
    row2_indices = [4, 5, 6]

    for idx, step_num in enumerate(row2_indices):
        col = 3 - idx
        x = start_x + col * (card_w + gap)
        y = row2_y
        name, desc, accent, bg = steps[step_num]

        draw.rounded_rectangle([x, y, x + card_w, y + card_h], radius=10, fill="#1E293B", outline=accent, width=2)
        draw.rounded_rectangle([x, y, x + card_w, y + 36], radius=8, fill=bg)
        draw.text((x + 12, y + 8), name, fill="#FFFFFF", font=get_font(True, 13))
        draw.text((x + 12, y + 48), desc, fill="#CBD5E1", font=get_font(False, 11), spacing=4)

        if idx < 2:
            arr_x = x - gap + 6
            arr_y = y + card_h // 2
            draw.line([x - 6, arr_y, arr_x, arr_y], fill="#64748B", width=3)
            draw.polygon([(arr_x, arr_y - 5), (arr_x - 6, arr_y), (arr_x, arr_y + 5)], fill="#64748B")

    # Final summary box
    final_x = start_x
    final_y = row2_y
    draw.rounded_rectangle([final_x, final_y, final_x + card_w, final_y + card_h], radius=10, fill="#064E3B", outline="#10B981", width=2)
    draw.text((final_x + 15, final_y + 20), "COMPLETED & AUDITED", fill="#34D399", font=get_font(True, 13))
    draw.text((final_x + 15, final_y + 48), "Leave Balance updated.\nPayslip LWP computed.\nAudit trail timestamped.\nEmployee notified via Mail.", fill="#A7F3D0", font=get_font(False, 11), spacing=4)

    draw.text((40, 480), "Database Flow: leaves table (PENDING -> APPROVED/REJECTED) -> employee_attendances -> payroll_batches", fill="#38BDF8", font=get_font(True, 12))

    img.save("public/frontent/assets/img/graphics/leave_management_workflow.png", "PNG")
    img.save("documents/graphics/leave_management_workflow.png", "PNG")
    print("Leave management workflow created!")

# -------------------------------------------------------------
# 2. PAYROLL PROCESS LIFECYCLE FLOWCHART
# -------------------------------------------------------------
def create_payroll_workflow():
    w, h = 1000, 520
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    draw.rectangle([0, 0, w, 75], fill="#0F172A")
    draw.text((35, 16), "EMS PAYROLL PROCESSING PIPELINE & CALCULATION ENGINE", fill="#FFFFFF", font=get_font(True, 20))
    draw.text((35, 46), "From Attendance Cut-Off & LWP Days to Statutory Deductions, Batch Approval & PDF Payslips", fill="#38BDF8", font=get_font(False, 12))

    stages = [
        ("STAGE 1: SALARY SETUP", "/masters/payroll/salary-structure\n* Assign Gross & CTC\n* Basic (50%), HRA (25%)\n* Allowances, PF, ESI, TDS", "#2563EB"),
        ("STAGE 2: ATTENDANCE CUT-OFF", "Month-End Attendance Lock\n* Total Calendar Days (30/31)\n* Attended Days = P + L + H\n* LWP Days = Working - Attended", "#7C3AED"),
        ("STAGE 3: BATCH GENERATION", "/payroll/process\n* Select Month, Year, Agency\n* Click [Generate Payroll]\n* Computes Payable Ratio", "#059669"),
        ("STAGE 4: BATCH REVIEW", "/payroll/batches\n* Review Gross, PF, ESI, TDS\n* Check Net in-hand amount\n* Click [Approve Batch]", "#D97706"),
        ("STAGE 5: DISBURSEMENT", "/payroll/batches/{id}\n* Select Bank Mode (NEFT/UTR)\n* Click [Disburse Selected]\n* Status -> 'Paid / Disbursed'", "#DC2626"),
        ("STAGE 6: PAYSLIPS & APP", "/payroll/payslips\n* A4 PDF Payslip with Words\n* Released to Employee App\n* Annual CTC Increments", "#0891B2"),
    ]

    cw, ch = 285, 175
    start_x, start_y = 35, 100
    h_gap, v_gap = 35, 30

    for i, (title, desc, color) in enumerate(stages):
        row = i // 3
        col = i % 3
        x = start_x + col * (cw + h_gap)
        y = start_y + row * (ch + v_gap)

        draw.rounded_rectangle([x, y, x + cw, y + ch], radius=10, fill="#F8FAFC", outline="#CBD5E1", width=1)
        draw.rounded_rectangle([x, y, x + cw, y + 36], radius=8, fill=color)
        draw.text((x + 12, y + 8), title, fill="#FFFFFF", font=get_font(True, 12))
        draw.text((x + 12, y + 48), desc, fill="#334155", font=get_font(False, 11), spacing=4)

        if col < 2:
            arr_x = x + cw + 8
            arr_y = y + ch // 2
            draw.line([arr_x, arr_y, arr_x + 18, arr_y], fill="#94A3B8", width=3)
            draw.polygon([(arr_x + 18, arr_y - 5), (arr_x + 25, arr_y), (arr_x + 18, arr_y + 5)], fill="#94A3B8")

    # Formula Box
    draw.rounded_rectangle([35, 435, w - 35, 500], radius=8, fill="#EFF6FF", outline="#BFDBFE", width=1)
    draw.text((45, 443), "CORE PAYROLL FORMULA:", fill="#1E40AF", font=get_font(True, 11))
    draw.text((45, 462), "Payable Days = Total Month Days - LWP Days  |  Earned Gross = (Gross / Month Days) x Payable Days  |  Net Salary = Earned Gross - (PF + ESI + PT + TDS)", fill="#1E3A8A", font=get_font(False, 10))

    img.save("public/frontent/assets/img/graphics/payroll_lifecycle_flowchart.png", "PNG")
    img.save("documents/graphics/payroll_lifecycle_flowchart.png", "PNG")
    print("Payroll workflow created!")

# -------------------------------------------------------------
# 3. EMS USER ROLES MATRIX (Focused purely on EMS, Leaves & Payroll)
# -------------------------------------------------------------
def create_ems_roles():
    w, h = 1000, 480
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    draw.rectangle([0, 0, w, 75], fill="#1E3A8A")
    draw.text((35, 16), "EMS USER ROLES & ACCESS MATRIX (EMPLOYEE, LEAVE & PAYROLL)", fill="#FFFFFF", font=get_font(True, 20))
    draw.text((35, 46), "5 Primary Roles Governing Staff Administration, Attendance, Leave Approvals & Payroll Disbursement", fill="#93C5FD", font=get_font(False, 12))

    roles = [
        ("1. SUPER ADMIN", "Guard: web | Role ID: 1", "Full system bypass. Creates admin users, assigns Spatie roles/permissions, manages all branches & salary settings.", "#DC2626"),
        ("2. SYSTEM ADMIN", "Guard: web | Role ID: 2", "Daily operations. Runs monthly Payroll batches, approves salary disbursement, manages Agencies, Shifts & Designations.", "#2563EB"),
        ("3. HR MANAGER", "Guard: web | Role ID: 3", "Staff onboarding & document verification. Approves/rejects Leave Requests, monitors attendance registers & holidays.", "#7C3AED"),
        ("4. SUPERVISOR / MANAGER", "Guard: web | Role ID: 6", "Team-level punch logs monitoring. Reviews subordinate team leaves, provides recommendations & team KPI reviews.", "#D97706"),
        ("5. EMPLOYEE", "Guard: employee | Role ID: 4", "Self-service dashboard. Punch In/Out with GPS coordinates, views Leave balances, applies leaves, downloads Payslips.", "#059669"),
    ]

    cw, ch = 176, 260
    start_x, start_y = 35, 105
    gap = 20

    for i, (name, guard, desc, color) in enumerate(roles):
        x = start_x + i * (cw + gap)
        y = start_y

        draw.rounded_rectangle([x, y, x + cw, y + ch], radius=10, fill="#F8FAFC", outline="#CBD5E1", width=1)
        draw.rounded_rectangle([x, y, x + cw, y + 42], radius=8, fill=color)
        draw.text((x + 10, y + 8), name, fill="#FFFFFF", font=get_font(True, 11))
        draw.text((x + 10, y + 24), guard, fill="#F1F5F9", font=get_font(False, 9))
        draw.text((x + 10, y + 55), desc, fill="#334155", font=get_font(False, 10), spacing=3)

    # Footer note
    draw.rounded_rectangle([35, 395, w - 35, 455], radius=8, fill="#F0FDF4", outline="#BBF7D0", width=1)
    draw.text((45, 405), "ROLE ISOLATION & SECURITY RULE:", fill="#166534", font=get_font(True, 11))
    draw.text((45, 425), "Employees cannot view other staff payslips. Supervisors only approve their mapped subordinates. Only Admin/HR can generate & disburse Payroll.", fill="#14532D", font=get_font(False, 10))

    img.save("public/frontent/assets/img/graphics/ems_user_roles.png", "PNG")
    img.save("documents/graphics/ems_user_roles.png", "PNG")
    print("EMS user roles matrix created!")

# -------------------------------------------------------------
# 4. SALARY STRUCTURE & PAYSLIP BREAKDOWN MOCKUP
# -------------------------------------------------------------
def create_salary_mockup():
    w, h = 1000, 460
    img = Image.new("RGB", (w, h), "#F8FAFC")
    draw = ImageDraw.Draw(img)

    draw.rectangle([0, 0, w, 70], fill="#0F172A")
    draw.text((35, 16), "EMPLOYEE SALARY STRUCTURE & CTC BREAKDOWN MATRIX", fill="#FFFFFF", font=get_font(True, 18))
    draw.text((35, 42), "Auto-Calculated Real-Time Formula: Monthly Gross -> Earnings -> Statutory Deductions -> Net In-Hand", fill="#94A3B8", font=get_font(False, 12))

    # Box 1: Earnings
    draw.rounded_rectangle([35, 95, 480, 360], radius=10, fill="#FFFFFF", outline="#E2E8F0", width=1)
    draw.rectangle([35, 95, 480, 135], fill="#1E3A8A")
    draw.text((50, 105), "MONTHLY EARNINGS HEADS", fill="#FFFFFF", font=get_font(True, 13))

    earnings = [
        ("Basic Salary", "50% of Monthly Gross (e.g. ₹25,000 on ₹50k Gross)"),
        ("House Rent Allowance (HRA)", "25% of Monthly Gross (e.g. ₹12,500)"),
        ("Conveyance Allowance", "Standard Fixed Allowance (₹2,000)"),
        ("Medical Allowance", "Standard Fixed Allowance (₹1,500)"),
        ("Special / Other Allowance", "Balancing component ensuring 100% Gross match"),
        ("Bike Allowance / Perks", "Optional mapped for field designations"),
    ]
    ey = 145
    for title, rule in earnings:
        draw.text((50, ey), f"• {title}:", fill="#0F172A", font=get_font(True, 10))
        draw.text((230, ey), rule, fill="#64748B", font=get_font(False, 9.5))
        ey += 34

    # Box 2: Deductions
    draw.rounded_rectangle([515, 95, 965, 360], radius=10, fill="#FFFFFF", outline="#E2E8F0", width=1)
    draw.rectangle([515, 95, 965, 135], fill="#B91C1C")
    draw.text((530, 105), "STATUTORY & OPTIONAL DEDUCTIONS", fill="#FFFFFF", font=get_font(True, 13))

    deductions = [
        ("PF Employee Contribution", "12% of Basic Salary (Statutory Cap ₹1,800/mo)"),
        ("ESI Employee Contribution", "0.75% of Gross (Applicable if Gross <= ₹21,000)"),
        ("Professional Tax (PT)", "State slab governed (e.g. ₹200/mo)"),
        ("Monthly TDS (Income Tax)", "Applicable slab based on annual projected CTC"),
        ("Loss of Pay (LWP)", "(Monthly Gross / Month Days) x LWP Days"),
        ("Loan / Advance Deduction", "Auto-deducted from monthly disbursement"),
    ]
    dy = 145
    for title, rule in deductions:
        draw.text((530, dy), f"• {title}:", fill="#0F172A", font=get_font(True, 10))
        draw.text((720, dy), rule, fill="#64748B", font=get_font(False, 9.5))
        dy += 34

    # Bottom Summary
    draw.rounded_rectangle([35, 380, w - 35, 440], radius=8, fill="#ECFDF5", outline="#A7F3D0", width=1)
    draw.text((50, 395), "NET PAYABLE IN-HAND SALARY = Total Monthly Earned Gross - Total Deductions", fill="#065F46", font=get_font(True, 12))
    draw.text((50, 415), "ANNUAL CTC = (Monthly Gross x 12) + (PF Employer 12% x 12) + (ESI Employer 3.25% x 12) + Annual Gratuity & Bonus", fill="#047857", font=get_font(False, 10))

    img.save("public/frontent/assets/img/graphics/salary_structure_mockup.png", "PNG")
    img.save("documents/graphics/salary_structure_mockup.png", "PNG")
    print("Salary structure mockup created!")

create_leave_workflow()
create_payroll_workflow()
create_ems_roles()
create_salary_mockup()
print("All 4 graphics successfully generated!")
