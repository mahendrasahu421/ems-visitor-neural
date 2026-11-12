<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of active holidays with pagination.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Holiday Management';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);

        // Filter ONLY active holidays
        $query = Holiday::where('status', 1);

        // Search filter (Holiday Name, Remark, Date)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('holiday_name', 'like', "%{$search}%")
                  ->orWhere('holiday_remark', 'like', "%{$search}%")
                  ->orWhere('holiday_date', 'like', "%{$search}%");
            });
        }

        // Category filter (common / restricted)
        if ($request->filled('type')) {
            $type = strtolower($request->input('type'));
            if ($type === 'common') {
                $query->where('holiday_remark', 'like', '%Common%');
            } elseif ($type === 'restricted') {
                $query->where('holiday_remark', 'like', '%Restricted%');
            }
        }

        // Year filter
        if ($request->filled('year') && $request->input('year') !== 'all') {
            $query->whereYear('holiday_date', (int) $request->input('year'));
        }

        // Month filter
        if ($request->filled('month') && $request->input('month') !== 'all') {
            $query->whereMonth('holiday_date', (int) $request->input('month'));
        }

        // Configurable pagination per page (default 10)
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 15, 25, 50, 100])) {
            $perPage = 10;
        }

        $data['holidays'] = $query->orderBy('holiday_date', 'asc')->paginate($perPage)->withQueryString();

        // Base query for active holiday statistics
        $baseActive = Holiday::where('status', 1);

        $data['totalActiveHolidays'] = (clone $baseActive)->count();
        $data['commonHolidaysCount'] = (clone $baseActive)->where('holiday_remark', 'like', '%Common%')->count();
        $data['restrictedHolidaysCount'] = (clone $baseActive)->where('holiday_remark', 'like', '%Restricted%')->count();

        // Upcoming holiday from today
        $todayDate = Carbon::today()->toDateString();
        $data['nextUpcomingHoliday'] = (clone $baseActive)
            ->where('holiday_date', '>=', $todayDate)
            ->orderBy('holiday_date', 'asc')
            ->first();

        // List of available active years for filter dropdown
        $data['availableYears'] = (clone $baseActive)
            ->selectRaw('YEAR(holiday_date) as yr')
            ->distinct()
            ->orderBy('yr', 'desc')
            ->pluck('yr');

        $data['currentSearch'] = $request->input('search', '');
        $data['currentType'] = $request->input('type', '');
        $data['currentYear'] = $request->input('year', '');
        $data['currentMonth'] = $request->input('month', '');
        $data['perPage'] = $perPage;

        return view('home.holiday.index', $data);
    }

    /**
     * Display interactive FullCalendar view of active holidays.
     */
    public function calendar(Request $request)
    {
        $data['title'] = 'Holiday Calendar';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);

        $baseActive = Holiday::where('status', 1);

        $data['totalActiveHolidays'] = (clone $baseActive)->count();
        $data['commonHolidaysCount'] = (clone $baseActive)->where('holiday_remark', 'like', '%Common%')->count();
        $data['restrictedHolidaysCount'] = (clone $baseActive)->where('holiday_remark', 'like', '%Restricted%')->count();

        // Upcoming holiday from today
        $todayDate = Carbon::today()->toDateString();
        $data['nextUpcomingHoliday'] = (clone $baseActive)
            ->where('holiday_date', '>=', $todayDate)
            ->orderBy('holiday_date', 'asc')
            ->first();

        // All active holidays for FullCalendar
        $holidays = (clone $baseActive)->orderBy('holiday_date', 'asc')->get();

        $events = [];
        foreach ($holidays as $h) {
            $isCommon = str_contains(strtolower($h->holiday_remark ?? ''), 'common');
            $events[] = [
                'id' => $h->id,
                'title' => ($isCommon ? '★ ' : '') . $h->holiday_name,
                'start' => $h->holiday_date,
                'className' => $isCommon ? 'holiday-event common-holiday' : 'holiday-event restricted-holiday',
                'extendedProps' => [
                    'name' => $h->holiday_name,
                    'category' => $isCommon ? 'Common' : 'Restricted',
                    'remark' => $h->holiday_remark ?? '—',
                    'date' => $h->holiday_date,
                    'day' => Carbon::parse($h->holiday_date)->format('l'),
                    'is_common' => $isCommon,
                ]
            ];
        }

        $data['events'] = $events;
        $data['holidays'] = $holidays;

        return view('home.holiday.calendar', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Create New Holiday';
        $data['imageUrl'] = "https://picsum.photos/200/200?random=" . rand(1, 1000);
        return view('home.holiday.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'holiday_name' => 'required|string|max:100',
            'holiday_date' => 'required|date',
            'holiday_type' => 'nullable|string|max:50',
            'holiday_remark' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        $type = $request->input('holiday_type');
        $desc = trim($request->input('description') ?? $request->input('holiday_remark') ?? '');

        if ($type === 'Common') {
            $finalRemark = 'Common Holiday - Mandatory Office Closed' . ($desc ? " ({$desc})" : '');
        } elseif ($type === 'Restricted') {
            $finalRemark = 'Restricted Holiday - Optional (Any 5)' . ($desc ? " ({$desc})" : '');
        } else {
            $finalRemark = $desc ?: 'Company Holiday';
        }

        Holiday::create([
            'holiday_name' => trim($request->input('holiday_name')),
            'holiday_date' => $request->input('holiday_date'),
            'holiday_remark' => \Illuminate\Support\Str::limit($finalRemark, 250),
            'status' => (int) $request->input('status', 1),
        ]);

        return redirect()->route('settings.holiday')->with('success', 'Holiday created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $holiday = Holiday::findOrFail($id);
        return response()->json(['success' => true, 'data' => $holiday]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $holiday = Holiday::findOrFail($id);
        $data['title'] = 'Edit Holiday';
        $data['holiday'] = $holiday;
        return view('home.holiday.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'holiday_name' => 'required|string|max:100',
            'holiday_date' => 'required|date',
            'holiday_remark' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        $holiday = Holiday::findOrFail($id);
        $holiday->update([
            'holiday_name' => trim($request->input('holiday_name')),
            'holiday_date' => $request->input('holiday_date'),
            'holiday_remark' => $request->input('description') ?? $request->input('holiday_remark') ?? $holiday->holiday_remark,
            'status' => (int) $request->input('status', 1),
        ]);

        return redirect()->route('settings.holiday')->with('success', 'Holiday updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return redirect()->route('settings.holiday')->with('success', 'Holiday deleted successfully.');
    }
}
