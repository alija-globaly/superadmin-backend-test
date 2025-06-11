<?php

use Illuminate\Database\Seeder;

class RPLAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        \Agentcis\PartnerDatabase\Model\Category::query()
            ->where("name", "=", "RPL Assessment")
            ->where("type", "=", "master")
            ->delete();

        \Agentcis\PartnerDatabase\Model\Category::create(
            [
                'name' => 'RPL Assessment',
                'partner_label' => 'RPL Assessment',
                'product_label' => 'RPL Assessment',
                'type' => 'master',
                'children' => [
                    ['name' => 'RPL Assessment', 'type' => 'partner'],
                ]
            ]);
    }
}
