<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class ProjectWorkflowController extends Controller
{
    /**
     * Render the interactive Project Workflow & Application Guide page.
     */
    public function index()
    {
        $docxName = 'EMS_Employee_Leave_Payroll_Complete_Workflow.docx';
        $docxPath = public_path($docxName);

        if (!File::exists($docxPath)) {
            $docxName = 'EMS_Visitor_Complete_Project_Workflow.docx';
            $docxPath = public_path($docxName);
        }

        $hasDocx = File::exists($docxPath);

        return view('docs.workflow', [
            'title' => 'EMS — Employee Management, Leave & Payroll Process Complete Operational Guide',
            'hasDocx' => $hasDocx,
            'docxUrl' => asset($docxName),
            'docxSize' => $hasDocx ? round(File::size($docxPath) / 1024, 1) . ' KB' : '230 KB',
        ]);
    }

    /**
     * Download the generated Word Document (.docx).
     */
    public function downloadDocx()
    {
        $docxName = 'EMS_Employee_Leave_Payroll_Complete_Workflow.docx';
        $path = public_path($docxName);
        
        if (!File::exists($path)) {
            $docxName = 'EMS_Visitor_Complete_Project_Workflow.docx';
            $path = public_path($docxName);
        }

        if (!File::exists($path)) {
            $path = base_path($docxName);
        }

        if (!File::exists($path)) {
            return redirect()->back()->with('error', 'Word document not found. Please regenerate.');
        }

        return Response::download($path, $docxName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }
}
