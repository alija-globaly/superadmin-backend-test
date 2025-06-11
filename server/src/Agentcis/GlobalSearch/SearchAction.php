<?php

namespace Agentcis\GlobalSearch;

use Agentcis\PartnerDatabase\Model\Partner;
use Agentcis\PartnerDatabase\Model\Product;
use Agentcis\Tenant\Model\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SearchAction
{
    public function __invoke(Request $request, SearchKeywordParser $searchKeywordParser)
    {
        // I know this can be refactored
        // Not the right time to refactor it in my view

        if (Str::contains($request->query('keyword'), ['@partner', '@product', '@tenant'])) {
            // user is searching via basic query lang we created :)
            $query = collect($searchKeywordParser->parse($request->query('keyword')))->groupBy('module');
            $result = [
                'partner' => [],
                'product' => [],
                'tenant' => [],
            ];
            $partnerSearchKeywords = Arr::pluck($query->get('@partner', []), 'keyword');
            $productSearchKeywords = Arr::pluck($query->get('@product', []), 'keyword');
            $tenantSearchKeywords = Arr::pluck($query->get('@tenant', []), 'keyword');
            if (!empty($partnerSearchKeywords)) {
                $result['partner'] = Partner::query()
                    ->withTrashed()
                    ->where(function ($query) use ($partnerSearchKeywords) {
                        foreach ($partnerSearchKeywords as $keyword) {
                            $query->where('name', 'like', '%' . $keyword . '%')
                                ->orWhere('email', 'like', '%' . $keyword . '%');
                        }
                    })->orderBy('name')->take(25)->get(['id', 'name', 'email']);
            }
            if (!empty($productSearchKeywords)) {
                $result['product'] = Product::query()
                    ->withTrashed()
                    ->with([
                        'partner' => function ($query) {
                            $query->withTrashed()->select('id', 'name', 'email');
                        }
                    ])
                    ->where(function ($query) use ($productSearchKeywords) {
                        foreach ($productSearchKeywords as $keyword) {
                            $query->where('name', 'like', '%' . $keyword . '%');
                        }
                    })->orderBy('name')->take(25)->get(['id', 'name', 'partner_id']);
            }
            if (!empty($tenantSearchKeywords)) {
                $result['tenant'] = Tenant::query()
                    ->where(function ($query) use ($tenantSearchKeywords) {
                        foreach ($tenantSearchKeywords as $keyword) {
                            $query->where('business_name', 'like', '%' . $keyword . '%')
                                ->orWhere('subdomain', 'like', '%' . $keyword . '%')
                                ->orWhere('email', 'like', '%' . $keyword . '%');
                        }
                    })->orderBy('business_name')->take(25)->get(['id', 'business_name', 'email', 'subscription_status as status']);
            }

            return ['data' => $result];
        }

        // looks like we have to do a dynamic search
        $keyword = $request->query('keyword');
        return [
            'data' => [
                'partner' => Partner::query()
                    ->withTrashed()
                    ->where(function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    })->orderBy('name')->take(20)->get(['id', 'name', 'email']),
                'product' => Product::query()
                    ->withTrashed()
                    ->with([
                        'partner' => function ($query) {
                            $query->select('id', 'name', 'email');
                        }
                    ])
                    ->where(function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    })->orderBy('name')->take(20)->get(['id', 'name', 'partner_id']),
                'tenant' => Tenant::query()
                    ->where(function ($query) use ($keyword) {
                        $query->where('business_name', 'like', '%' . $keyword . '%')
                            ->orWhere('subdomain', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    })->orderBy('business_name')->take(20)->get(['id', 'business_name', 'email', 'subscription_status as status'])
            ]
        ];
    }
}
