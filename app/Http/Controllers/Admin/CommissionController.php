<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionConfig;
use App\Models\User;
use App\Models\Role;
use App\Traits\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    use Notifiable;

    /**
     * Display commission settings
     */
    public function index(Request $request)
    {
        $commissions = CommissionConfig::all()->keyBy('service_type');

        // Get filter parameters
        $filterRole = $request->get('role', 'all');
        $filterStatus = $request->get('status', 'all');

        return view('admin.commissions.index', compact('commissions', 'filterRole', 'filterStatus'));
    }

    /**
     * Update commission rates
     */
    public function update(Request $request)
    {
        $request->validate([
            'HOSTEL' => 'required|numeric|min:0|max:100',
            'APARTMENT' => 'required|numeric|min:0|max:100',
            'FOOD' => 'required|numeric|min:0|max:100',
            'LAUNDRY' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        
        try {
            $oldRates = [];
            $newRates = [];

            foreach (['HOSTEL', 'APARTMENT', 'FOOD', 'LAUNDRY'] as $type) {
                $oldConfig = CommissionConfig::where('service_type', $type)->first();
                $oldRates[$type] = $oldConfig ? $oldConfig->rate : 0;

                $config = CommissionConfig::updateOrCreate(
                    ['service_type' => $type],
                    [
                        'rate' => $request->$type,
                        'updated_at' => now()
                    ]
                );
                
                $newRates[$type] = $config->rate;
            }
            
            DB::commit();

            // Send notifications to affected users based on role
            $this->sendCommissionUpdateNotifications($oldRates, $newRates, $request);

            return redirect()->route('admin.commissions.index')
                ->with('success', 'Commission rates updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update commission rates. ' . $e->getMessage());
        }
    }

    /**
     * Send notifications to users when commission rates change
     */
    private function sendCommissionUpdateNotifications($oldRates, $newRates, $request)
    {
        $changedServices = [];
        
        // Check which services actually changed
        foreach (['HOSTEL', 'APARTMENT', 'FOOD', 'LAUNDRY'] as $type) {
            if ($oldRates[$type] != $newRates[$type]) {
                $changedServices[] = [
                    'type' => $type,
                    'old' => $oldRates[$type],
                    'new' => $newRates[$type]
                ];
            }
        }

        if (empty($changedServices)) {
            return; // No changes, no notifications needed
        }

        // Determine which roles to notify based on filter
        $notifyRoles = [];
        
        if ($request->has('notify_roles')) {
            $notifyRoles = $request->notify_roles;
        } else {
            // Default: notify all affected roles
            $affectedRoles = [];
            foreach ($changedServices as $change) {
                switch ($change['type']) {
                    case 'HOSTEL':
                    case 'APARTMENT':
                        $affectedRoles[] = 'OWNER';
                        break;
                    case 'FOOD':
                        $affectedRoles[] = 'FOOD';
                        break;
                    case 'LAUNDRY':
                        $affectedRoles[] = 'LAUNDRY';
                        break;
                }
            }
            $notifyRoles = array_unique($affectedRoles);
        }

        // Get users with specific roles
        $users = User::whereHas('roles', function($q) use ($notifyRoles) {
            $q->whereIn('name', $notifyRoles);
        })->get();

        // Create notification message
        $changes = [];
        foreach ($changedServices as $change) {
            $serviceName = ucfirst(strtolower($change['type']));
            $changes[] = "{$serviceName}: {$change['old']}% → {$change['new']}%";
        }

        $message = "Commission rates have been updated:\n" . implode("\n", $changes);

        // Send notification to each user
        foreach ($users as $user) {
            $this->createNotification(
                $user->id,
                'SYSTEM',
                'Commission Rates Updated',
                $message,
                'commission',
                null
            );
        }

        // Log the notification
        \Log::info("Commission update notifications sent to " . count($users) . " users");
    }

    /**
     * Get commission rate for a specific service
     */
    public function getRate($type)
    {
        $commission = CommissionConfig::where('service_type', strtoupper($type))->first();
        
        return response()->json([
            'rate' => $commission ? $commission->rate : 0
        ]);
    }

    /**
     * Calculate commission for an amount
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:HOSTEL,APARTMENT,FOOD,LAUNDRY',
            'amount' => 'required|numeric|min:0'
        ]);

        $commission = CommissionConfig::where('service_type', $request->type)->first();
        
        if (!$commission) {
            return response()->json(['error' => 'Commission rate not found'], 404);
        }

        $commissionAmount = ($request->amount * $commission->rate) / 100;
        $totalAmount = $request->amount + $commissionAmount;

        return response()->json([
            'rate' => $commission->rate,
            'commission_amount' => round($commissionAmount, 2),
            'total_amount' => round($totalAmount, 2)
        ]);
    }

    /**
     * Reset to default rates
     */
    public function reset(Request $request)
    {
        $defaults = [
            'HOSTEL' => 5.00,
            'APARTMENT' => 3.00,
            'FOOD' => 8.00,
            'LAUNDRY' => 10.00,
        ];

        DB::beginTransaction();
        
        try {
            $oldRates = [];
            foreach (['HOSTEL', 'APARTMENT', 'FOOD', 'LAUNDRY'] as $type) {
                $oldConfig = CommissionConfig::where('service_type', $type)->first();
                $oldRates[$type] = $oldConfig ? $oldConfig->rate : 0;
            }

            foreach ($defaults as $type => $rate) {
                CommissionConfig::updateOrCreate(
                    ['service_type' => $type],
                    ['rate' => $rate]
                );
            }
            
            DB::commit();

            // Send notifications
            $this->sendCommissionUpdateNotifications($oldRates, $defaults, $request);

            return redirect()->route('admin.commissions.index')
                ->with('success', 'Commission rates reset to defaults!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reset commission rates.');
        }
    }
}