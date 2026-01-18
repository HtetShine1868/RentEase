<?php


namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Booking;

class CurrentRentals extends Component
{
    public $bookings;
    
    public function mount()
    {
        $this->bookings = Booking::where('user_id', auth()->id())
            ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
            ->with('property')
            ->latest()
            ->take(5)
            ->get();
    }
    
    public function render()
    {
        return view('livewire.dashboard.current-rentals');
    }
}
