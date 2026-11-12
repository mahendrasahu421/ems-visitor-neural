<?php

namespace App\Services\Payroll;

use App\Models\CountryState;

class SalaryStateRuleService
{
    /**
     * States that do NOT levy Professional Tax (PT Rate = ₹0)
     * e.g., Delhi, Haryana, Uttar Pradesh, Rajasthan, Punjab, Himachal Pradesh, Uttarakhand, Goa, UTs
     */
    protected const NO_PT_STATE_KEYWORDS = [
        'delhi',
        'haryana',
        'uttar pradesh',
        'up',
        'rajasthan',
        'punjab',
        'himachal',
        'uttarakhand',
        'chandigarh',
        'goa',
        'jammu',
        'kashmir',
        'ladakh',
        'andaman',
        'nicobar',
        'arunachal',
        'nagaland',
        'mizoram',
        'manipur',
        'dadra',
        'daman',
        'diu',
        'lakshadweep',
    ];

    /**
     * Metro States / Urban hubs for higher HRA slab (50% of Basic)
     * Delhi NCR, Maharashtra (Mumbai/Pune), West Bengal (Kolkata), Tamil Nadu (Chennai), Karnataka (Bangalore)
     */
    protected const METRO_STATE_KEYWORDS = [
        'delhi',
        'maharashtra',
        'karnataka',
        'tamil nadu',
        'west bengal',
    ];

    /**
     * Resolve state name or CountryState model from ID or name.
     */
    public function resolveStateName($state): string
    {
        if (empty($state)) {
            return '';
        }

        if (is_numeric($state)) {
            $record = CountryState::find((int) $state);
            return $record ? trim($record->name) : '';
        }

        return trim((string) $state);
    }

    /**
     * Determine if a state levies Professional Tax (PT).
     */
    public function isPtApplicable($state): bool
    {
        $name = strtolower($this->resolveStateName($state));
        if ($name === '') {
            return true; // Default fallback to standard statutory
        }

        foreach (self::NO_PT_STATE_KEYWORDS as $keyword) {
            if (str_contains($name, $keyword)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate monthly Professional Tax (PT) based on State and Monthly Gross.
     */
    public function calculateStatePt($state, float $monthlyGross): float
    {
        if (!$this->isPtApplicable($state)) {
            return 0.00;
        }

        $name = strtolower($this->resolveStateName($state));

        // State-specific statutory threshold slabs
        if (str_contains($name, 'maharashtra')) {
            // Maharashtra: Gross > 10,000 => ₹200 (₹300 in Feb)
            return ($monthlyGross > 10000) ? 200.00 : 0.00;
        }

        if (str_contains($name, 'karnataka')) {
            // Karnataka: Gross >= 25,000 => ₹200
            return ($monthlyGross >= 25000) ? 200.00 : 0.00;
        }

        if (str_contains($name, 'madhya pradesh') || str_contains($name, 'mp')) {
            // Madhya Pradesh: Gross >= 18,750 => ₹208 (~₹200)
            return ($monthlyGross >= 18750) ? 200.00 : 0.00;
        }

        if (str_contains($name, 'gujarat')) {
            // Gujarat: Gross >= 12,000 => ₹200
            return ($monthlyGross >= 12000) ? 200.00 : 0.00;
        }

        if (str_contains($name, 'andhra') || str_contains($name, 'telangana')) {
            // AP / Telangana: Gross >= 20,000 => ₹200
            return ($monthlyGross >= 20000) ? 200.00 : 0.00;
        }

        if (str_contains($name, 'west bengal')) {
            if ($monthlyGross > 25000) return 200.00;
            if ($monthlyGross > 15000) return 150.00;
            if ($monthlyGross > 10000) return 130.00;
            return 0.00;
        }

        // Standard national threshold fallback
        return ($monthlyGross >= 15000) ? 200.00 : 0.00;
    }

    /**
     * Check if posting state is considered a Metro tier for HRA calculation.
     */
    public function isMetro($state): bool
    {
        $name = strtolower($this->resolveStateName($state));
        if ($name === '') {
            return false;
        }

        foreach (self::METRO_STATE_KEYWORDS as $keyword) {
            if (str_contains($name, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate HRA % (of Basic Salary) based on Metro vs Non-Metro state.
     * Metro: 50% of Basic (Tax exemption standard)
     * Non-Metro: 40% of Basic
     */
    public function getHraPercentageOfBasic($state): float
    {
        return $this->isMetro($state) ? 50.00 : 40.00;
    }

    /**
     * Get complete state payroll profile summary.
     */
    public function getStatePayrollProfile($state, float $sampleGross = 35000.00): array
    {
        $name = $this->resolveStateName($state);
        $ptApplicable = $this->isPtApplicable($state);
        $ptAmount = $this->calculateStatePt($state, $sampleGross);
        $isMetro = $this->isMetro($state);
        $hraPctOfBasic = $this->getHraPercentageOfBasic($state);

        return [
            'state_name' => $name ?: 'Default State',
            'pt_applicable' => $ptApplicable,
            'pt_amount' => $ptAmount,
            'is_metro' => $isMetro,
            'hra_percentage_of_basic' => $hraPctOfBasic,
            'hra_percentage_of_gross' => round($hraPctOfBasic * 0.50, 2), // assuming 50% basic
            'pt_note' => $ptApplicable ? "PT of ₹{$ptAmount} applies on gross ₹{$sampleGross}" : "No Professional Tax (₹0)",
            'hra_note' => $isMetro ? "Metro Zone (50% of Basic)" : "Non-Metro Zone (40% of Basic)",
        ];
    }
}
