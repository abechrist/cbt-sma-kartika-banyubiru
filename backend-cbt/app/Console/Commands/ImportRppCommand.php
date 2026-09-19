<?php

namespace App\Console\Commands;

use App\Services\RppIntegrationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rpp:import {file : Path to JSON file containing RPP data}')]
#[Description('Import RPP from JSON file and integrate to LMS and CBT modules')]
class ImportRppCommand extends Command
{
    protected $signature = 'rpp:import {file}';

    protected $description = 'Import RPP from JSON file and integrate to LMS and CBT modules';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (! file_exists($file)) {
            $this->error("File {$file} not found.");

            return self::FAILURE;
        }

        $jsonContent = file_get_contents($file);

        if ($jsonContent === false) {
            $this->error("Failed to read file {$file}.");

            return self::FAILURE;
        }

        $rppData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: '.json_last_error_msg());

            return self::FAILURE;
        }

        // Validate required structure
        if (! isset($rppData['rpp_metadata'], $rppData['lms_integration'], $rppData['cbt_integration'])) {
            $this->error('Invalid RPP structure. Must contain rpp_metadata, lms_integration, and cbt_integration.');

            return self::FAILURE;
        }

        $this->info('Importing RPP...');
        $this->info("Subject: {$rppData['rpp_metadata']['mata_pelajaran']}");
        $this->info("Class: {$rppData['rpp_metadata']['kelas']}");
        $this->info("Topic: {$rppData['rpp_metadata']['topik_utama']}");

        try {
            $service = app(RppIntegrationService::class);
            $rpp = $service->importRpp($rppData);

            $this->newLine();
            $this->info('✓ RPP imported successfully!');
            $this->info("RPP ID: {$rpp->id}");
            $this->info("Subject: {$rpp->subject->name}");
            $this->info("Topic: {$rpp->topic}");
            $this->info("Status: {$rpp->status}");
            $this->newLine();
            $this->info('Integrated resources:');
            $this->info("  LMS Materials: {$rpp->materials()->count()}");
            $this->info("  CBT Assessments: {$rpp->assessments()->count()}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
