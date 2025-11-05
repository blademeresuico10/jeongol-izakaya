<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\table;
use App\Models\Reservation;
use App\Models\walkin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerTableComponent extends Component
{
    public $tables = [];
    public $selectedTable = null;
    public $showModal = false;
    public $filterStatus = 'all'; 
    public $searchDate;
    public $searchTime;
    public $showDateTimeSearch = false;

    protected $listeners = ['tableStatusUpdated' => 'loadTables'];

    public function mount()
    {
        $this->searchDate = now()->format('Y-m-d');
        $this->searchTime = now()->format('H:i');
        $this->loadTables();
    }

    public function loadTables()
    {
        $availabilityData = $this->checkAvailability();
        
        if ($this->filterStatus !== 'all') {
            $this->tables = collect($availabilityData)->filter(function($table) {
                return match($this->filterStatus) {
                    'available' => $table['is_available'],
                    'occupied' => $table['is_active'],
                    'pending' => $table['is_pending'],
                    default => true
                };
            })->values()->toArray();
        } else {
            $this->tables = $availabilityData;
        }
    }

    public function checkAvailability()
    {
        if (!$this->searchDate || !$this->searchTime) {
            return [];
        }

        $searchDateTime = Carbon::parse("{$this->searchDate} {$this->searchTime}");
        $now = Carbon::now();

        $tables = Table::all();
        $availabilityData = [];

        foreach ($tables as $table) {
            $isCurrentlyOccupied = Reservation::where('table_id', $table->id)
                ->where('status', 'Active')
                ->where('started_at', '<=', $now)
                ->where('ended_at', '>=', $now)
                ->exists();

            $isActiveReservation = Reservation::where('table_id', $table->id)
                ->where('status', 'Active')
                ->where('started_at', '<=', $searchDateTime)
                ->where('ended_at', '>=', $searchDateTime)
                ->exists();

            $isPendingReservation = Reservation::where('table_id', $table->id)
                ->where('status', 'Pending')
                ->where('started_at', '<=', $searchDateTime)
                ->where('ended_at', '>=', $searchDateTime)
                ->exists();

            $isBookedWalkin = Walkin::where('table_id', $table->id)
                ->where('status', 'active')
                ->where('started_at', '<=', $searchDateTime)
                ->where('ended_at', '>=', $searchDateTime)
                ->exists();

            $availabilityData[] = [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'is_available' => !($isActiveReservation || $isBookedWalkin || $isPendingReservation),
                'is_pending' => $isPendingReservation,
                'is_active' => $isActiveReservation || $isBookedWalkin,
                'is_currently_occupied' => $isCurrentlyOccupied
            ];
        }

        return $availabilityData;
    }

    public function isWithinOperatingHours()
    {
        if (!$this->searchDate || !$this->searchTime) {
            return false;
        }

        try {
            $selectedDateTime = Carbon::parse("{$this->searchDate} {$this->searchTime}");

            $operatingHours = DB::table('operating_hours')
                ->where('date', $this->searchDate)
                ->first();

            if (!$operatingHours) {
                $operatingHours = DB::table('operating_hours')
                    ->where('is_default', true)
                    ->first();
            }

            if (!$operatingHours || $operatingHours->is_closed) {
                return false;
            }

            $openTime = Carbon::parse("{$this->searchDate} " . $operatingHours->open_time);
            $closeTime = Carbon::parse("{$this->searchDate} " . $operatingHours->close_time);

            if ($closeTime->lessThan($openTime)) {
                $closeTime->addDay();
                if ($selectedDateTime->format('H:i') < $openTime->format('H:i')) {
                    $selectedDateTime->addDay();
                }
            }

            return $selectedDateTime->between($openTime, $closeTime);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function selectTable($tableId)
    {
        // Check operating hours before allowing selection
        if (!$this->isWithinOperatingHours()) {
            session()->flash('error', 'The selected time is outside operating hours. Please choose a different time.');
            return;
        }

        $this->selectedTable = collect($this->tables)->firstWhere('id', $tableId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTable = null;
    }

    public function updatedFilterStatus()
    {
        $this->loadTables();
    }

    public function updatedSearchDate()
    {
        $this->loadTables();
    }

    public function updatedSearchTime()
    {
        $this->loadTables();
    }

    public function toggleDateTimeSearch()
    {
        $this->showDateTimeSearch = !$this->showDateTimeSearch;
    }

    public function resetToNow()
    {
        $this->searchDate = now()->format('Y-m-d');
        $this->searchTime = now()->format('H:i');
        $this->loadTables();
    }

    public function getStatusColor($table)
    {
        if ($table['is_available']) {
            return 'bg-green-100 border-green-300';
        } elseif ($table['is_pending']) {
            return 'bg-yellow-100 border-yellow-300';
        } elseif ($table['is_active']) {
            return 'bg-red-100 border-red-300';
        }
        return 'bg-gray-100 border-gray-300';
    }

    public function getStatusText($table)
    {
        if ($table['is_available']) {
            return 'Available';
        } elseif ($table['is_pending']) {
            return 'Pending';
        } elseif ($table['is_active']) {
            return 'Occupied';
        }
        return 'Unknown';
    }

    public function render()
    {
        return view('livewire.customer-table-component');
    }
}