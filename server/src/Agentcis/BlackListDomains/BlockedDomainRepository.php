<?php

namespace Agentcis\BlackListDomains;

interface BlockedDomainRepository
{
    public function getAll();

    public function add(array $domain);

    public function delete($domain);

    public function update($domain, $oldDomain);
}
