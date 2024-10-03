<?php 
namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class OrderReportService
{
    public function generate(array $data)
    {
        $startDate = Carbon::parse($data['start_date'])->startOfDay();
        $endDate = Carbon::parse($data['end_date'])->endOfDay();

        // Fetch orders between the start and end date
        $orders = Order::with('orderItems.menuItem', 'guest', 'table', 'user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $reports = [];
        $totalAmount = 0;
        $totalServiceCharge = 0;

        // Loop through orders and accumulate data
        foreach ($orders as $order) {
            $orderTotal = $order->total_amount;
            $totalAmount += $orderTotal;
            $totalServiceCharge += $order->service_charge;

            foreach ($order->orderItems as $item) {
                $reports[] = [
                    // 'selling_price' => $this->formatCurrency($detail->price / $detail->qty),

                    'order_id' => $this->formatCurrency($order->id),
                    'menu_item_name' => $item->menuItem->name,
                    'quantity' =>  $this->formatCurrency($item->quantity),
                    'price' =>  $this->formatCurrency($item->price),
                    'total_price' =>  $this->formatCurrency($item->quantity * $item->price),
                ];
            }
        }

        // Create header, footer, and report data
        $header = [
            'restaurant_name' => 'WHITE24 PALACE',
            'start_date' => $startDate->format('d F Y'),
            'end_date' => $endDate->format('d F Y'),
        ];

        $footer = [
            'total_amount' => $this->formatCurrency($totalAmount),
            'total_service_charge' => $this->formatCurrency($totalServiceCharge),
        ];

        return [
            'reports' => $reports,
            'header' => $header,
            'footer' => $footer,
        ];
        
    }
    private function formatCurrency($value)
    {
        return Number::format($value);
    }
}
