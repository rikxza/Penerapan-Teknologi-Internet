<?php
use App\Models\Transaction;

// Rename 'Add Budget:' to 'Alokasi Budget :'
Transaction::where('description', 'like', 'Add Budget:%')
    ->update(['description' => DB::raw("REPLACE(description, 'Add Budget:', 'Alokasi Budget :')")]);

echo "Renamed transactions successfully.";
