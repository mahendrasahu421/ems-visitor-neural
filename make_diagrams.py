import os
from PIL import Image, ImageDraw, ImageFont

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
# 1. VISITOR WORKFLOW DIAGRAM (Step-by-step pipeline)
# -------------------------------------------------------------
def create_visitor_workflow():
    w, h = 1000, 520
    img = Image.new("RGB", (w, h), "#0F172A") # Dark modern background
    draw = ImageDraw.Draw(img)

    # Header
    draw.text((40, 25), "EMS VISITOR GATE PASS MANAGEMENT - LIFECYCLE WORKFLOW", fill="#38BDF8", font=get_font(True, 22))
    draw.text((40, 58), "Complete End-to-End Operational Pipeline: Arrival -> Verification -> Approval -> Check-In -> Check-Out", fill="#94A3B8", font=get_font(False, 13))

    steps = [
        ("1. ARRIVAL & FORM", "Visitor arrives at gate.\nEnters Name, Phone, ID,\nPurpose & Host Officer.", "#3B82F6", "#1E3A8A"),
        ("2. VERIFY & PHOTO", "Webcam snapshot captured.\nMobile OTP authentication\nverifies real phone number.", "#8B5CF6", "#4C1D95"),
        ("3. HOST APPROVAL", "Host employee gets alert.\nClicks [Approve] to permit\nor [Reject] to decline entry.", "#EC4899", "#831843"),
        ("4. PASS GENERATION", "System issues Pass Token.\nEncodes unique QR code\nwith visitor & host metadata.", "#F59E0B", "#78350F"),
        ("5. PRINT BADGE", "Thermal/A4 badge printed.\nVisitor wears physical pass\nwith photo & barcode.", "#10B981", "#064E3B"),
        ("6. CHECK-IN (GATE)", "Security scans QR badge.\nStatus -> 'Inside Premises'.\nOverstay timer initiated.", "#06B6D4", "#164E63"),
        ("7. EXIT CHECK-OUT", "Visitor returns to exit gate.\nGuard scans QR code.\nStatus -> 'Checked-Out'.", "#6366F1", "#312E81"),
    ]

    # Row 1: 4 steps
    card_w, card_h = 210, 150
    gap = 26
    start_x, start_y = 40, 110

    for i in range(4):
        x = start_x + i * (card_w + gap)
        y = start_y
        name, desc, accent, bg = steps[i]
        
        # Card background & border
        draw.rounded_rectangle([x, y, x + card_w, y + card_h], radius=10, fill="#1E293B", outline=accent, width=2)
        # Top banner
        draw.rounded_rectangle([x, y, x + card_w, y + 36], radius=8, fill=bg)
        draw.text((x + 12, y + 8), name, fill="#FFFFFF", font=get_font(True, 13))
        # Description
        draw.text((x + 12, y + 48), desc, fill="#CBD5E1", font=get_font(False, 11), spacing=4)

        # Arrow to next
        if i < 3:
            arrow_x = x + card_w + 5
            arrow_y = y + card_h // 2
            draw.line([arrow_x, arrow_y, arrow_x + 16, arrow_y], fill="#64748B", width=3)
            draw.polygon([(arrow_x + 16, arrow_y - 5), (arrow_x + 22, arrow_y), (arrow_x + 16, arrow_y + 5)], fill="#64748B")

    # Down arrow from Step 4 to Step 5
    down_x = start_x + 3 * (card_w + gap) + card_w // 2
    draw.line([down_x, start_y + card_h + 6, down_x, start_y + card_h + 36], fill="#64748B", width=3)
    draw.polygon([(down_x - 5, start_y + card_h + 36), (down_x, start_y + card_h + 42), (down_x + 5, start_y + card_h + 36)], fill="#64748B")

    # Row 2: Steps 5, 6, 7 (reversed direction right-to-left)
    row2_y = start_y + card_h + 48
    row2_indices = [4, 5, 6]

    for idx, step_num in enumerate(row2_indices):
        col = 3 - idx  # 3, 2, 1
        x = start_x + col * (card_w + gap)
        y = row2_y
        name, desc, accent, bg = steps[step_num]

        draw.rounded_rectangle([x, y, x + card_w, y + card_h], radius=10, fill="#1E293B", outline=accent, width=2)
        draw.rounded_rectangle([x, y, x + card_w, y + 36], radius=8, fill=bg)
        draw.text((x + 12, y + 8), name, fill="#FFFFFF", font=get_font(True, 13))
        draw.text((x + 12, y + 48), desc, fill="#CBD5E1", font=get_font(False, 11), spacing=4)

        # Left arrow
        if idx < 2:
            arr_x = x - gap + 6
            arr_y = y + card_h // 2
            draw.line([x - 6, arr_y, arr_x, arr_y], fill="#64748B", width=3)
            draw.polygon([(arr_x, arr_y - 5), (arr_x - 6, arr_y), (arr_x, arr_y + 5)], fill="#64748B")

    # Final completed badge
    final_x = start_x
    final_y = row2_y
    draw.rounded_rectangle([final_x, final_y, final_x + card_w, final_y + card_h], radius=10, fill="#064E3B", outline="#10B981", width=2)
    draw.text((final_x + 15, final_y + 25), "SUCCESS / LOGGED", fill="#34D399", font=get_font(True, 14))
    draw.text((final_x + 15, final_y + 55), "Visit Marked Completed.\nTotal duration calculated.\nPass auto-deactivated.\nAudit record archived.", fill="#A7F3D0", font=get_font(False, 11), spacing=4)

    # Footer note
    draw.text((40, 480), "Live Database Status: PENDING_APPROVAL -> APPROVED -> CHECKED_IN -> CHECKED_OUT", fill="#38BDF8", font=get_font(True, 12))

    img.save("documents/graphics/visitor_workflow_diagram.png", "PNG")
    print("Visitor workflow diagram created successfully!")

# -------------------------------------------------------------
# 2. USER ROLES HIERARCHY DIAGRAM
# -------------------------------------------------------------
def create_user_roles_diagram():
    w, h = 1000, 520
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Border
    draw.rectangle([0, 0, w - 1, h - 1], outline="#E2E8F0", width=2)

    # Header
    draw.rectangle([0, 0, w, 75], fill="#1E3A8A")
    draw.text((35, 16), "EMS & VISITOR SYSTEM - USER ROLES & ACCESS MATRIX", fill="#FFFFFF", font=get_font(True, 20))
    draw.text((35, 46), "Multi-Guard Architecture: Web (Admin/HR), Employee Portal, Client Guard, Gate Security", fill="#93C5FD", font=get_font(False, 12))

    roles = [
        ("SUPER ADMIN", "Guard: web | Role ID: 1", "Full system master bypass, Role & Permission setup, User creation, Database migrations & logs.", "#DC2626"),
        ("SYSTEM ADMIN", "Guard: web | Role ID: 2", "Daily operations, Masters (Shift, Branch, Dept), Payroll batch approval, Leave policies, Reports.", "#2563EB"),
        ("HR MANAGER", "Guard: web | Role ID: 3", "Employee onboarding, Document vault verification, Leave approvals, Attendance registers, Jobs.", "#7C3AED"),
        ("SUPERVISOR", "Guard: web | Role ID: 6", "Team-level punch logs review, Subordinate leave recommendations, Performance evaluation.", "#D97706"),
        ("EMPLOYEE", "Guard: employee | Role: 4", "Self-service dashboard, GPS punch in/out, Apply leaves, Payslip PDF download, PMT appraisal.", "#059669"),
        ("CLIENT", "Guard: client", "Dedicated Client Portal, Project issue ticketing, Real-time status tracking, Ticket replies.", "#0891B2"),
        ("GATE SECURITY", "Role: Security Desk", "Visitor entry form, Webcam photo snap, Mobile OTP verify, Badge printing, QR check-out.", "#4B5563"),
        ("VISITOR / GUEST", "Role: Guest / Vendor", "Pass holder, Receives printed badge with QR code, Physical identification across campus.", "#64748B"),
    ]

    cw, ch = 220, 185
    start_x, start_y = 35, 95
    h_gap, v_gap = 20, 20

    for i, (name, guard, desc, color) in enumerate(roles):
        row = i // 4
        col = i % 4
        x = start_x + col * (cw + h_gap)
        y = start_y + row * (ch + v_gap)

        draw.rounded_rectangle([x, y, x + cw, y + ch], radius=8, fill="#F8FAFC", outline="#E2E8F0", width=1)
        # Accent top bar
        draw.rounded_rectangle([x, y, x + cw, y + 6], radius=4, fill=color)
        
        # Role Name & Tag
        draw.text((x + 12, y + 15), name, fill=color, font=get_font(True, 14))
        draw.text((x + 12, y + 36), guard, fill="#64748B", font=get_font(False, 10))

        # Divider
        draw.line([x + 12, y + 54, x + cw - 12, y + 54], fill="#E2E8F0", width=1)

        # Description
        draw.text((x + 12, y + 62), desc, fill="#334155", font=get_font(False, 11), spacing=3)

    img.save("documents/graphics/user_roles_matrix.png", "PNG")
    print("User roles matrix diagram created successfully!")

# -------------------------------------------------------------
# 3. PRINTED VISITOR GATE PASS MOCKUP
# -------------------------------------------------------------
def create_visitor_badge_mockup():
    w, h = 500, 680
    img = Image.new("RGB", (w, h), "#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Badge outer border with dashed cut line
    draw.rectangle([10, 10, w - 10, h - 10], outline="#CBD5E1", width=2)

    # Header Bar
    draw.rectangle([12, 12, w - 12, 90], fill="#0F172A")
    draw.text((30, 24), "NEURAL INFO SOLUTIONS", fill="#FFFFFF", font=get_font(True, 18))
    draw.text((30, 52), "VISITOR GATE PASS / SECURITY CLEARANCE", fill="#38BDF8", font=get_font(True, 11))

    # Pass ID Box
    draw.rounded_rectangle([30, 105, w - 30, 150], radius=6, fill="#EFF6FF", outline="#BFDBFE", width=1)
    draw.text((45, 115), "PASS NUMBER:", fill="#1E40AF", font=get_font(False, 10))
    draw.text((45, 128), "VP-2026-10492", fill="#1E3A8A", font=get_font(True, 15))
    draw.text((280, 115), "DATE: 20-OCT-2026", fill="#1E40AF", font=get_font(True, 11))
    draw.text((280, 130), "VALID: 10:15 AM - 01:30 PM", fill="#059669", font=get_font(True, 10))

    # Photo & Details Section
    # Photo box
    draw.rectangle([30, 165, 160, 315], fill="#F1F5F9", outline="#94A3B8", width=1)
    draw.text((50, 220), "[ VISITOR ]\n[  PHOTO  ]", fill="#64748B", font=get_font(True, 12), spacing=4, align="center")
    draw.rectangle([30, 290, 160, 315], fill="#059669")
    draw.text((42, 295), "ID VERIFIED (OTP)", fill="#FFFFFF", font=get_font(True, 9))

    # Details
    dx = 175
    details = [
        ("VISITOR NAME", "RAHUL SHARMA"),
        ("PHONE NUMBER", "+91 98765 43210"),
        ("GOVT ID PROOF", "Aadhaar: **** **** 9012"),
        ("PERSON TO MEET", "Amit Kumar (Dev Lead)"),
        ("DEPARTMENT", "Information Technology"),
        ("PURPOSE OF VISIT", "Technical Interview (L2)"),
    ]

    cur_y = 165
    for lbl, val in details:
        draw.text((dx, cur_y), lbl, fill="#64748B", font=get_font(False, 9))
        draw.text((dx, cur_y + 13), val, fill="#0F172A", font=get_font(True, 11))
        cur_y += 31

    # Divider
    draw.line([30, 335, w - 30, 335], fill="#E2E8F0", width=1)

    # QR Code Placeholder Box
    draw.rounded_rectangle([30, 350, w - 30, 520], radius=8, fill="#F8FAFC", outline="#E2E8F0", width=1)
    
    # Fake QR Code pattern
    qr_x, qr_y = 50, 365
    draw.rectangle([qr_x, qr_y, qr_x + 130, qr_y + 130], fill="#FFFFFF", outline="#000000", width=2)
    # Inner QR blocks
    draw.rectangle([qr_x + 10, qr_y + 10, qr_x + 45, qr_y + 45], fill="#000000")
    draw.rectangle([qr_x + 85, qr_y + 10, qr_x + 120, qr_y + 45], fill="#000000")
    draw.rectangle([qr_x + 10, qr_y + 85, qr_x + 45, qr_y + 120], fill="#000000")
    draw.rectangle([qr_x + 60, qr_y + 60, qr_x + 75, qr_y + 75], fill="#000000")
    draw.rectangle([qr_x + 80, qr_y + 80, qr_x + 115, qr_y + 115], fill="#000000")

    draw.text((200, 380), "DIGITAL SCANNER TOKEN", fill="#1E3A8A", font=get_font(True, 12))
    draw.text((200, 405), "* Scan at Main Security Gate 1\n* Scan upon exit to record check-out\n* Keep badge visibly displayed at\n  all times inside company campus.", fill="#475569", font=get_font(False, 10), spacing=4)

    # Signature lines
    draw.line([30, 580, 190, 580], fill="#94A3B8", width=1)
    draw.text((50, 588), "Visitor Signature", fill="#64748B", font=get_font(False, 10))

    draw.line([w - 190, 580, w - 30, 580], fill="#94A3B8", width=1)
    draw.text((w - 170, 588), "Security Officer Sign", fill="#64748B", font=get_font(False, 10))

    # Footer notice
    draw.rectangle([12, h - 55, w - 12, h - 12], fill="#FEF2F2")
    draw.text((30, h - 45), "WARNING: Non-transferable pass. Property of Neural Info Solutions.\nLoss of pass must be immediately reported to Security Desk.", fill="#B91C1C", font=get_font(False, 9), spacing=2)

    img.save("documents/graphics/visitor_badge_mockup.png", "PNG")
    print("Visitor badge mockup created successfully!")

# -------------------------------------------------------------
# 4. ADMIN KPI METRICS DASHBOARD MOCKUP
# -------------------------------------------------------------
def create_dashboard_mockup():
    w, h = 1000, 400
    img = Image.new("RGB", (w, h), "#F8FAFC")
    draw = ImageDraw.Draw(img)

    # Header
    draw.rectangle([0, 0, w, 70], fill="#0F172A")
    draw.text((35, 15), "EMS HR & OPERATIONS PORTAL - EXECUTIVE DASHBOARD", fill="#FFFFFF", font=get_font(True, 18))
    draw.text((35, 42), "Live Overview: Active Staff, Attendance, Leaves & Real-time Operations", fill="#94A3B8", font=get_font(False, 12))

    # Metric Cards
    cards = [
        ("ACTIVE EMPLOYEES", "1,248", "+12 this month", "#2563EB", "#EFF6FF"),
        ("PRESENT TODAY", "1,182", "94.7% Attendance", "#059669", "#ECFDF5"),
        ("LATE IN TODAY", "19", "Needs review", "#D97706", "#FFFBEB"),
        ("PENDING LEAVES", "8", "Awaiting approval", "#DC2626", "#FEF2F2"),
        ("VISITORS INSIDE", "14", "Campus Gate 1 & 2", "#7C3AED", "#F5F3FF"),
    ]

    card_w = 172
    card_h = 110
    start_x = 35
    gap = 17

    for i, (title, val, sub, color, bg) in enumerate(cards):
        x = start_x + i * (card_w + gap)
        y = 90
        draw.rounded_rectangle([x, y, x + card_w, y + card_h], radius=10, fill="#FFFFFF", outline="#E2E8F0", width=1)
        # Left accent stripe
        draw.rectangle([x, y + 10, x + 4, y + card_h - 10], fill=color)
        draw.text((x + 14, y + 14), title, fill="#64748B", font=get_font(True, 9))
        draw.text((x + 14, y + 34), val, fill="#0F172A", font=get_font(True, 22))
        draw.text((x + 14, y + 78), sub, fill=color, font=get_font(False, 10))

    # Action Shortcuts Banner
    draw.rounded_rectangle([35, 220, w - 35, 370], radius=12, fill="#FFFFFF", outline="#E2E8F0", width=1)
    draw.text((55, 238), "QUICK ACTION BUTTONS (DIRECT SYSTEM MODULE SHORTCUTS)", fill="#1E293B", font=get_font(True, 13))

    buttons = [
        ("[ ➕ Add Employee ]", "Register new employee profile & code", "#2563EB"),
        ("[ 📄 Document Vault ]", "Verify Aadhaar, PAN & certificates", "#7C3AED"),
        ("[ 📅 Attendance Register ]", "Export monthly XLS/PDF attendance", "#059669"),
        ("[ 💰 Payroll Process ]", "Generate & approve monthly salary", "#D97706"),
        ("[ 🚪 Visitor Gate Pass ]", "Check-in guest, webcam & badge", "#0891B2"),
    ]

    btn_w = 168
    btn_h = 75
    btn_start_x = 55
    btn_gap = 16

    for i, (btn_text, desc, color) in enumerate(buttons):
        bx = btn_start_x + i * (btn_w + btn_gap)
        by = 270
        draw.rounded_rectangle([bx, by, bx + btn_w, by + btn_h], radius=8, fill="#F8FAFC", outline="#CBD5E1", width=1)
        draw.text((bx + 8, by + 12), btn_text, fill=color, font=get_font(True, 11))
        draw.text((bx + 8, by + 36), desc, fill="#64748B", font=get_font(False, 9), spacing=2)

    img.save("documents/graphics/admin_dashboard_mockup.png", "PNG")
    print("Dashboard mockup created successfully!")

# Execute all
create_visitor_workflow()
create_user_roles_diagram()
create_visitor_badge_mockup()
create_dashboard_mockup()
print("All graphics successfully generated!")
