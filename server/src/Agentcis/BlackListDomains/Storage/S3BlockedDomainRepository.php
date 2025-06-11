<?php

namespace Agentcis\BlackListDomains\Storage;

use Agentcis\BlackListDomains\BlockedDomainRepository;
use Illuminate\Support\Facades\Storage;

class S3BlockedDomainRepository implements BlockedDomainRepository
{
    private $fileName = 'agentcis_blocked_domains.json';

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        if (Storage::disk('s3')->exists($this->fileName)) {
            return;
        }
        Storage::disk('s3')
            ->put($this->fileName, '');
    }

    public function add(array $domains)
    {
        $domains = $this->getAll()->merge($domains)->unique();
        Storage::disk('s3')->put($this->fileName, $domains->toJson());

        return $domains->toArray();
    }

    public function getAll()
    {
        return collect(json_decode(
            Storage::disk('s3')->get($this->fileName),
            JSON_OBJECT_AS_ARRAY
        ));
    }

    public function update($domain, $oldDomain)
    {
        $domains = collect(json_decode(
            Storage::disk('s3')->get($this->fileName),
            JSON_OBJECT_AS_ARRAY
        ));
        Storage::disk('s3')
            ->put(
                $this->fileName,
                $domains->reject(function ($domain) use ($oldDomain) {
                    return $domain == $oldDomain;
                })->push($domain)->unique()->values()->toJson());
    }

    public function delete($domain)
    {
        $domains = collect(json_decode(
            Storage::disk('s3')->get($this->fileName),
            JSON_OBJECT_AS_ARRAY
        ));
        Storage::disk('s3')
            ->put(
                $this->fileName,
                $domains->reject(function ($d) use ($domain) {
                    return $d == $domain;
                })->unique()->values()->toJson());
    }
}
