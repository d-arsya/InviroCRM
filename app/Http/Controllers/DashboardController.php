<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Traits\ApiResponder;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponder;

    /**
     * Chart data for dashboard
     *
     * Get data this week
     */
    #[Group('Dashboard')]
    public function chart()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $rawStats = Customer::select(
            DB::raw('DATE(date) as day'),
            DB::raw('COUNT(*) as customer'),
            DB::raw('SUM(total_count) as total_count'),
            DB::raw('SUM(total_price) as total_price')
        )
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Build full week from Monday to Sunday
        $stats = [
            'customer' => [],
            'quantity' => [],
            'income' => [],
        ];

        foreach (Carbon::now()->startOfWeek()->daysUntil(Carbon::now()->endOfWeek()) as $day) {
            $dateKey = $day->toDateString();

            $stats['customer'][] = isset($rawStats[$dateKey]) ? (int) $rawStats[$dateKey]->customer : 0;
            $stats['quantity'][] = isset($rawStats[$dateKey]) ? (int) $rawStats[$dateKey]->total_count : 0;
            $stats['income'][] = isset($rawStats[$dateKey]) ? (int) $rawStats[$dateKey]->total_price : 0;
        }

        return $this->success($stats);
    }

    /**
     * Card data for dashboard by date
     *
     * @param  string  $date  YYYY-MM-DD ex. 2025-09-10
     */
    #[Group('Dashboard')]
    public function card(string $date)
    {
        $customer = Customer::where('date', $date)->get();
        $message = Customer::whereSend($date)->whereStatus('sended')->count();

        return $this->success([
            'customer' => $customer->count(),
            'quantity' => $customer->sum('total_count'),
            'income' => $customer->sum('total_price'),
            'message' => $message,
        ]);
    }
}
