<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Display the Privacy Policy page.
     */
    public function privacyPolicy(Request $request)
    {
        $data = [
            'title' => 'Privacy Policy - EMS Visitor & Employee Portal',
            'companyName' => 'Neural Info Solutions Pvt. Ltd.',
            'appName' => 'EMS Visitor & Employee Management System',
            'effectiveDate' => 'November 1, 2026',
            'lastUpdated' => 'November 6, 2026',
            'contactEmail' => 'info@neuralinfo.org',
            'supportPhone' => '+91 88747 52747',
            'address' => '144-A IInd Floor, Vikas Nagar, Kanpur, Uttar Pradesh - 208027, India',
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return view('legal.privacy-policy', $data);
    }

    /**
     * Display the Terms and Conditions page.
     */
    public function termsConditions(Request $request)
    {
        $data = [
            'title' => 'Terms & Conditions - EMS Visitor & Employee Portal',
            'companyName' => 'Neural Info Solutions Pvt. Ltd.',
            'appName' => 'EMS Visitor & Employee Management System',
            'effectiveDate' => 'November 1, 2026',
            'lastUpdated' => 'November 6, 2026',
            'contactEmail' => 'info@neuralinfo.org',
            'supportPhone' => '+91 88747 52747',
            'address' => '144-A IInd Floor, Vikas Nagar, Kanpur, Uttar Pradesh - 208027, India',
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return view('legal.terms-and-conditions', $data);
    }
}
