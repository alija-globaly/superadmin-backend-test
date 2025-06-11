<?php


namespace Agentcis\PartnerDatabase;


class FeeTerms
{
    public function toArray()
    {
        return [
            ['id' => 1, 'name' => 'Full Fee'],
            ['id' => 2, 'name' => 'Per Year'],
            ['id' => 3, 'name' => 'Per Month'],
            ['id' => 4, 'name' => 'Per Term'],
            ['id' => 5, 'name' => 'Per Trimester'],
            ['id' => 6, 'name' => 'Per Semester'],
            ['id' => 7, 'name' => 'Per Week'],
            ['id' => 8, 'name' => 'Installment'],
        ];
    }
}
