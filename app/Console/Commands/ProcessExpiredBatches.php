<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ingredientBatch;

class ProcessExpiredBatches extends Command
{
    protected $signature = 'batches:process-expired';
    protected $description = 'Expired Ingredient Batches Processing';

    public function handle()
    {
        $this->info('Processing expired batches...');
        
        try {
            $result = ingredientBatch::processExpiredBatches();
            
            $this->info("Processed: {$result['processed']} batches");
            $this->info($result['message']);
            
            
            return 0; 
        } catch (\Exception $e) {
            $this->error('Failed: ' . $e->getMessage());
            
            return 1; 
        }
    }
}