<?php

namespace Agentcis\PartnerDatabase;

class WebHookEvent
{
    const PARTNER_UPDATED = 'partner_updated';
    const PARTNER_STATUS_CHANGED = 'partner_status_changed';
    const PARTNER_DELETED = 'partner_deleted';
    const PARTNER_BRANCH_ADDED = 'partner_branch_added';
    const PARTNER_BRANCH_UPDATED = 'partner_branch_updated';
    const PARTNER_BRANCH_STATUS_CHANGED = 'partner_branch_status_changed';
    const PARTNER_BRANCH_DELETED = 'partner_branch_deleted';
    const PARTNER_PRODUCT_ADDED = 'partner_product_added';
    const PARTNER_PRODUCT_UPDATED = 'partner_product_updated';
    const PARTNER_PRODUCT_STATUS_CHANGED = 'partner_product_status_changed';
    const PARTNER_PRODUCT_DELETED = 'partner_product_deleted';
    const PARTNER_PRODUCT_FEE_UPDATED = 'partner_product_fee_updated';
    const PARTNER_PRODUCT_TEST_SCORE_UPDATED = 'partner_product_test_score_updated';
}
