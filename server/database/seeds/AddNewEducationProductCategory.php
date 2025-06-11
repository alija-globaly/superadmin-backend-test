<?php

use Illuminate\Database\Seeder;

class AddNewEducationProductCategory extends Seeder
{
    public function run()
    {
        // done this way as i'm not sure CategoriesTableSeeder seeds corrects data as this seeder  truncates the current table
        // may be new id will be generated and that might cause issue
        // if this one is not problem we can simple add inside categoriesTableSeeder

        $educationCategory = \Agentcis\PartnerDatabase\Model\Category::query()
            ->where('name', 'Education')
            ->whereNull('parent_id')
            ->first();

        \Agentcis\PartnerDatabase\Model\Category::query()
            ->create(
                [
                    'parent_id' => $educationCategory->id,
                    'name' => 'Foundation Course',
                    'type' => 'product',
                ]
            );
    }
}
