<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestMinioConnection extends Command
{
    protected $signature = 'test:minio';
    protected $description = 'Test MinIO connection';

    public function handle()
    {
        $this->info('Testing MinIO connection...');
        
        try {
            $filename = 'test-' . time() . '.txt';
            $content = 'Test file created at ' . now();
            
            $this->info('Attempting to write file: ' . $filename);
            $result = Storage::disk('s3')->put($filename, $content);
            
            if ($result) {
                $this->info('File written successfully.');
                $this->info('File URL: ' . Storage::disk('s3')->url($filename));
                
                if (Storage::disk('s3')->exists($filename)) {
                    $this->info('File exists check: PASSED');
                    $this->info('File content: ' . Storage::disk('s3')->get($filename));
                } else {
                    $this->error('File exists check: FAILED');
                }
            } else {
                $this->error('Failed to write file.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
        }
        
        return Command::SUCCESS;
    }
}