<?php

namespace Agentcis;

use Agentcis\Auth\Actions\ProfileAction;
use Agentcis\BlackListDomains\Actions\AddNewDomainAction;
use Agentcis\BlackListDomains\Actions\DeleteDomainAction;
use Agentcis\BlackListDomains\Actions\GetAllBlackListDomainAction;
use Agentcis\BlackListDomains\Actions\UpdateDomainAction;
use Agentcis\Dashboard\Actions\TenantRegistrationReportAction;
use Agentcis\PartnerDatabase\Actions\GetMasterCategoryWithData;
use Agentcis\PartnerDatabase\Actions\ImportEventDetailAction;
use Agentcis\PartnerDatabase\Actions\ImportEventListAction;
use Agentcis\PartnerDatabase\Actions\SearchAction;
use Agentcis\Sms\Actions\AddCreditToPhoneNumber;
use Agentcis\Sms\Actions\ListPhoneNumbers;
use Agentcis\Sms\Actions\ListSms;
use Agentcis\Sms\Actions\ListSmsCreditLogs;
use Agentcis\Sms\Actions\SmsRegistrationDetail;
use Agentcis\Sms\Actions\SmsRegistrationStatusChange;
use Agentcis\Talent\Actions\AddTalentDetail;
use Agentcis\Talent\Actions\TalentDetailAction;
use Agentcis\Talent\Actions\TalentListAction;
use Agentcis\Talent\Actions\ToggleTalentStatus;
use Agentcis\Talent\Actions\UpdateTalentDetailAction;
use Agentcis\Tenant\Actions\ListTenantConfigOptions;
use Agentcis\Tenant\Actions\TenantCacheClear;
use Agentcis\Tenant\Actions\TogglePaymentNoticeBanner;
use Agentcis\WebhookEvent\Actions\DetailAction;
use Agentcis\WebhookEvent\Actions\ListAction;
use Illuminate\Routing\Router;

class Routes
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function __invoke()
    {
        $this->router->get('/master-categories', GetMasterCategoryWithData::class);


        // Authentication
//        $this->router->get('/partner-product-database/import', PartnerDatabase\Actions\ImportAction::class);

        $this->router->get('partner-product-database/search', SearchAction::class);
        $this->router->get('partner-product-database/product/{product_id}', PartnerDatabase\Actions\ProductDetailForWebhookAction::class);
        $this->router->get('/partners-available-countries',
            PartnerDatabase\Actions\AvailableCountriesListAction::class);
        $this->router->post('/login', Auth\Actions\LoginAction::class);
        $this->router->get('/categories', PartnerDatabase\Actions\CategoryListAction::class);


        $this->router->group(['middleware' => ['api', 'auth', 'active.user']], function (Router $router) {
            $this->router->post('/logout', Auth\Actions\LogoutAction::class);

            // Tenants Database
            $router->group(['middleware' => 'role:Observer,Admin'], function (Router $router) {
                $router->get('/dashboard', TenantRegistrationReportAction::class);
                $router->get('/tenants', Tenant\Actions\ListAction::class);
                $router->get('/tenants/{tenant_id}', Tenant\Actions\DetailAction::class);
                $router->get('/tenants/{tenant_id}/users', Tenant\Actions\TenantUserListAction::class);
                $router->get('/tenants/{tenant_id}/usage', Tenant\Actions\TenantFeatureUsageReportAction::class);
                $router->get('/tenants/{tenant_id}/config', ListTenantConfigOptions::class);
                $router->get('/tenants/{tenant_id}/cache-clear', TenantCacheClear::class);
                $router->put('/tenants/{tenant_id}/{status}', TogglePaymentNoticeBanner::class);
            });
            $router->group(['middleware' => 'role:Admin'], function (Router $router) {
                $router->get('/freemium-applications', FreemiumApplication\Actions\ListFreemiumApplication::class);
                $router->get('/freemium-applications/{id}', FreemiumApplication\Actions\DetailFreemiumApplication::class);
                $router->post('/freemium-application/{id}/approve', FreemiumApplication\Actions\ApproveFreemiumApplication::class);

                $router->get('/tenant/invitations', Tenant\Actions\InvitationListAction::class);
                $router->post('/tenant/invite', Tenant\Actions\InviteTenantAction::class);
                $router->delete('/tenant/invite/{id}', Tenant\Actions\InvitationDeleteAction::class);

                $this->router->get('blacklist-domains', GetAllBlackListDomainAction::class);
                $this->router->post('blacklist-domains', AddNewDomainAction::class);
                $this->router->put('blacklist-domains', UpdateDomainAction::class);
                $this->router->delete('blacklist-domains/{domain}', DeleteDomainAction::class);


                $this->router->group([
                    'prefix' =>'sms'
                ], function (Router  $router) {
                    $router->get('/registrations', ListSms::class);
                    $router->get('/registrations/{registrationId}', SmsRegistrationDetail::class);
                    $router->post('/change-status', SmsRegistrationStatusChange::class);
                    $router->post('/add-credit', AddCreditToPhoneNumber::class);
                    $router->get('/credit-logs', ListSmsCreditLogs::class);
                    $router->get('/phone-numbers', ListPhoneNumbers::class);
                });
            });

            $this->router->get('search', GlobalSearch\SearchAction::class);

            // Partner Product Database
//            $router->get('/categories', \Agentcis\PartnerDatabase\Actions\CategoryListAction::class);
            $router->get('/me', ProfileAction::class);
            $router->post('/partner-product-database/import', PartnerDatabase\Actions\ImportAction::class);
            $router->get('/partners', PartnerDatabase\Actions\PartnersListAction::class);
            $router->get('/degree-levels', PartnerDatabase\Actions\DegreeLevelsListAction::class);
            $router->get('/partner/{partner_id}', PartnerDatabase\Actions\PartnerDetailAction::class);
            $router->get('/partner/{partner_id}/branches',
                PartnerDatabase\Actions\PartnerBranchesListAction::class);
            $router->get('/partner/{partner_id}/products',
                PartnerDatabase\Actions\PartnerProductsListAction::class);
            $router->get('/product/{product_id}', PartnerDatabase\Actions\ProductDetailAction::class);
            $router->get('/branch/{branch_id}', PartnerDatabase\Actions\BranchDetailAction::class);

            $router->group(['middleware' => 'role:Data Specialist,Admin'], function (Router $router) {
                $router->post('/partner', PartnerDatabase\Actions\PartnerAddAction::class);
                $router->post('/branch/', PartnerDatabase\Actions\BranchAddAction::class);
                $router->post('/product/', PartnerDatabase\Actions\ProductAddAction::class);
                $router->post('/partner/{partner_id}/deactivate',
                    PartnerDatabase\Actions\DeActivatePartnerAction::class);
                $router->post('/partner/{partner_id}/activate',
                    PartnerDatabase\Actions\ActivatePartnerAction::class);
                $router->post('/branch/{branch_id}/deactivate',
                    PartnerDatabase\Actions\DeActivatePartnerBranchAction::class);
                $router->post('/branch/{branch_id}/activate',
                    PartnerDatabase\Actions\ActivatePartnerBranchAction::class);
                $router->post('/product/{branch_id}/deactivate',
                    PartnerDatabase\Actions\DeActivateProductAction::class);
                $router->post('/product/{branch_id}/activate',
                    PartnerDatabase\Actions\ActivateProductAction::class);

                $router->put('/product/{product_id}',
                    PartnerDatabase\Actions\ProductDetailUpdateAction::class);
                $router->put('/product/{product_id}/fees',
                    PartnerDatabase\Actions\ProductFeeUpdateAction::class);
                $router->put('/product/{product_id}/test-scores',
                    PartnerDatabase\Actions\ProductTestScoresUpdateAction::class);
                $router->put('/branch/{branch_id}', PartnerDatabase\Actions\BranchUpdateAction::class);
                $router->put('/partner/{partner_id}',
                    PartnerDatabase\Actions\PartnerDetailUpdateAction::class);

                $router->delete('/partner/{partner_id}', PartnerDatabase\Actions\PartnerDeleteAction::class);
                $router->delete('/product/{product_id}', PartnerDatabase\Actions\ProductDeleteAction::class);
                $router->delete('/branch/{branch_id}', PartnerDatabase\Actions\BranchDeleteAction::class);
            });

            // Talents
            $router->group(['middleware' => 'role:Observer,Admin'], function (Router $router) {
                $router->get('/talents', TalentListAction::class);
                $router->get('/talent/{id}', TalentDetailAction::class);
            });

            $router->group(['middleware' => 'role:Admin'], function (Router $router) {
                $router->post('/talent', AddTalentDetail::class);
                $router->post('/talent/{id}', UpdateTalentDetailAction::class);
                $router->post('/talent/{id}/toggle-status', ToggleTalentStatus::class);
            });

            // Webhook Events
            $router->group(['middleware' => 'role:Admin,Data Specialist'], function (Router $router) {
                $router->get('/import-events', ImportEventListAction::class);
                $router->get('/import-event/{id}', ImportEventDetailAction::class);
            });
            // Import Events
            $router->group(['middleware' => 'role:Admin'], function (Router $router) {
                $router->get('/events', ListAction::class);
                $router->get('/event/{id}', DetailAction::class);
            });
        });
    }
}
