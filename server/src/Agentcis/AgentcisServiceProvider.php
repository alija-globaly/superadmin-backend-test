<?php

namespace Agentcis;

use Agentcis\BlackListDomains\BlockedDomainRepository;
use Agentcis\BlackListDomains\Storage\S3BlockedDomainRepository;
use Agentcis\GlobalSearch\SearchKeywordParser;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\RouteRegistrar;
use Illuminate\Support\Facades\Event;
use Nette\Tokenizer\Tokenizer;

class AgentcisServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Agentcis\PartnerDatabase\Events\PartnerDetailUpdated' => [
            'Agentcis\PartnerDatabase\Handlers\SendPartnerDetailUpdatedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\PartnerStatusChanged' => [
            'Agentcis\PartnerDatabase\Handlers\SendPartnerStatusChangedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\PartnerDeleted' => [
            'Agentcis\PartnerDatabase\Handlers\SendPartnerDeletedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\NewBranchAdded' => [
            'Agentcis\PartnerDatabase\Handlers\SendBranchAddedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\BranchDetailUpdated' => [
            'Agentcis\PartnerDatabase\Handlers\SendBranchDetailUpdatedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\BranchStatusUpdated' => [
            'Agentcis\PartnerDatabase\Handlers\SendBranchStatusChangedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\BranchDeleted' => [
            'Agentcis\PartnerDatabase\Handlers\SendBranchDeletedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\NewProductDetailAdded' => [
            'Agentcis\PartnerDatabase\Handlers\SendProductAddedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\ProductDetailUpdated' => [
            'Agentcis\PartnerDatabase\Handlers\SendProductDetailUpdatedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\ProductFeeUpdated' => [
            'Agentcis\PartnerDatabase\Handlers\SendProductFeeUpdatedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\ProductTestScoreUpdated' => [
            'Agentcis\PartnerDatabase\Handlers\SendProductTestScoreUpdatedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\ProductStatusUpdated' => [
            'Agentcis\PartnerDatabase\Handlers\SendProductStatusChangedNotification',
        ],
        'Agentcis\PartnerDatabase\Events\ProductDeleted' => [
            'Agentcis\PartnerDatabase\Handlers\SendProductDeletedNotification',
        ],
        'Spatie\WebhookServer\Events\WebhookCallFailedEvent' => [
            'Agentcis\PartnerDatabase\Handlers\RecordWebhookStatus',
        ],
        'Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent' => [
            'Agentcis\PartnerDatabase\Handlers\RecordWebhookStatus',
        ],
        'Spatie\WebhookServer\Events\WebhookCallSucceededEvent' => [
            'Agentcis\PartnerDatabase\Handlers\RecordWebhookStatus',
        ],
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $router = $this->app['router'];

        (new Routes($router))();

        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
        if ($this->app->runningInConsole()) {
            $this->commands([
                Tenant\Commands\GenerateFeatureUsageReport::class
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::routes(function (RouteRegistrar $registrar) {
            $registrar->forAccessTokens();
        });
        $this->app->bind(BlockedDomainRepository::class, S3BlockedDomainRepository::class);
        $this->app->when(SearchKeywordParser::class)
            ->needs(Tokenizer::class)
            ->give(function () {
                return new Tokenizer([
                    SearchKeywordParser::T_MODULE => '(@partner|@product|@tenant)',
                    SearchKeywordParser::T_WHITESPACE => '\s+',
                    SearchKeywordParser::T_SEARCH_KEYWORD => '[a-zA-Z0-9_@.]+',
                ]);
            });
    }

    public function listens()
    {
        return $this->listen;
    }
}
