<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('categories')->truncate();
        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Education',
            'partner_label' => 'Institution',
            'product_label' => 'Courses',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'Institution', 'type' => 'partner'],
                ['name' => 'University', 'type' => 'partner'],
                ['name' => 'College', 'type' => 'partner'],
                ['name' => 'High School', 'type' => 'partner'],
                ['name' => 'School', 'type' => 'partner'],
                ['name' => 'Campus', 'type' => 'partner'],
                ['name' => 'Training Center', 'type' => 'partner'],
                // product category
                ['name' => 'Course', 'type' => 'product'],
                ['name' => 'Short Course', 'type' => 'product'],
                ['name' => 'Higher Education Course', 'type' => 'product'],
                ['name' => 'VET Course', 'type' => 'product'],
                ['name' => 'Degree', 'type' => 'product'],
            ]
        ]);
        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Visa & Migration',
            'partner_label' => 'Visa Office',
            'product_label' => 'Visa Subclass',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'Visa Office', 'type' => 'partner'],
                ['name' => 'Visa Department', 'type' => 'partner'],
                // product category
                ['name' => 'Immigration', 'type' => 'product'],
                ['name' => 'Visa Subclass', 'type' => 'product'],
            ]
        ]);

        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Insurance',
            'partner_label' => 'Insurance Provider',
            'product_label' => 'Insurance Policy',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'Insurance Provider', 'type' => 'partner'],
                // product category
                ['name' => 'Insurance policy', 'type' => 'product'],
                ['name' => 'Life Insurance', 'type' => 'product'],
                ['name' => 'Health Insurance', 'type' => 'product'],
                ['name' => 'Personal Insurance', 'type' => 'product'],
                ['name' => 'Property Insurance', 'type' => 'product'],
                ['name' => 'Marine Insurance', 'type' => 'product'],
                ['name' => 'Fire Insurance', 'type' => 'product'],
                ['name' => 'Liability Insurance', 'type' => 'product'],
                ['name' => 'Automobile Insurance', 'type' => 'product'],
            ]
        ]);

        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Accommodation',
            'partner_label' => 'Accommodation Provider',
            'product_label' => 'Accommodation Option',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'Accommodation Provider', 'type' => 'partner'],
                // product category
                ['name' => 'Hotel', 'type' => 'product'],
                ['name' => 'Hostel', 'type' => 'product'],
                ['name' => 'Apartment', 'type' => 'product'],
                ['name' => 'HomeStay', 'type' => 'product'],
                ['name' => 'Rental Term', 'type' => 'product'],
                ['name' => 'Student Accommodation', 'type' => 'product'],
            ]
        ]);
        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Short Classes',
            'partner_label' => 'Class Provider',
            'product_label' => 'Class',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'Training Center', 'type' => 'partner'],
                ['name' => 'Internal Instructor', 'type' => 'partner'],
                // product category
                ['name' => 'Class Option', 'type' => 'product'],
                ['name' => 'Internal Class', 'type' => 'product'],
                ['name' => 'English Class', 'type' => 'product'],
            ]
        ]);
        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Skill Assessment',
            'partner_label' => 'Assessment Body',
            'product_label' => 'Skill Assessment',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'Private', 'type' => 'partner'],
                ['name' => 'Government', 'type' => 'partner'],
                // product category
                ['name' => 'Skill Assessment', 'type' => 'product'],
            ]
        ]);
        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Other Services',
            'partner_label' => 'Service Provider',
            'product_label' => 'Service',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'Service Provider', 'type' => 'partner'],
                ['name' => 'Internal Other Department', 'type' => 'partner'],
                // product category
                ['name' => 'Internal Service', 'type' => 'product'],
                ['name' => 'External Service', 'type' => 'product'],
            ]
        ]);
        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Tours & Travel',
            'partner_label' => 'Service Provider',
            'product_label' => 'Service',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'Tour Provider', 'type' => 'partner'],
                ['name' => 'Airlines', 'type' => 'partner'],
                ['name' => 'Hotels', 'type' => 'partner'],
                // product category
                ['name' => 'Short Tours', 'type' => 'product'],
                ['name' => 'Air Ticket', 'type' => 'product'],
                ['name' => 'Short Hotel Accommodation', 'type' => 'product'],
            ]
        ]);
        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Tax & Accounting',
            'partner_label' => 'Service Provider',
            'product_label' => 'Service',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'Tax Agent', 'type' => 'partner'],
                ['name' => 'CA Firm', 'type' => 'partner'],
                ['name' => 'CPA Firm', 'type' => 'partner'],
                ['name' => 'Accounting Firm', 'type' => 'partner'],
                // product category
                ['name' => 'Tax Return', 'type' => 'product'],
                ['name' => 'BAS Return', 'type' => 'product'],
                ['name' => 'Accounting', 'type' => 'product'],
                ['name' => 'Bookkeeping', 'type' => 'product'],
            ]
        ]);
        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Professional Year',
            'partner_label' => 'PY Provider',
            'product_label' => 'PY Course',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'PY Provider', 'type' => 'partner'],
                // product category
                ['name' => 'Accounting Professional Year', 'type' => 'product'],
                ['name' => 'IT Professional Year', 'type' => 'product'],
            ]
        ]);
        \Agentcis\PartnerDatabase\Model\Category::create([
            'name' => 'Legal & Court',
            'partner_label' => 'Service Provider',
            'product_label' => 'Service',
            'type' => 'master',
            'children' => [
                // partner category
                ['name' => 'Court', 'type' => 'partner'],
                ['name' => 'Tribunal', 'type' => 'partner'],
                // product category
                ['name' => 'Decision', 'type' => 'product'],
                ['name' => 'Legal Case', 'type' => 'product'],
            ]
        ]);
    }
}
