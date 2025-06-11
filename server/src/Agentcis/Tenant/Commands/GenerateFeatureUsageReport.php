<?php

namespace Agentcis\Tenant\Commands;

use Illuminate\Console\Command;
use Agentcis\Tenant\Reports\FeatureUsageReport;

class GenerateFeatureUsageReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feature-usage:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate feature usage report group by customer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(FeatureUsageReport $featureUsageReport) {

        $featureUsageReport();
    
    }
}
