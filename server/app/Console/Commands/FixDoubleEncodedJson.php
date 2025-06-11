<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDoubleEncodedJson extends Command
{
    protected $signature = 'fix:double-encoded-json';
    protected $description = 'Fix double-encoded JSON in specific columns of the products table';

    public function handle()
    {
        $idsToFix = [
            183100, 183101, 183102, 183103, 183104, 183105, 183106, 183107,
            183110, 183111, 183112, 183113, 183114, 183115, 183116, 183117,
            183118, 183119, 183120, 183121, 183122, 183123, 183124, 183125,
            183127, 183128, 183129, 183130, 183131, 183132, 183133, 183134,
            183135, 183136, 183137, 183138, 183139, 183140, 183141, 183142,
            183143, 183144, 183145, 183146, 183148, 183149, 183150, 183168,
            183169, 183170, 183172, 183173, 183174, 183175, 183176, 183177,
            183178, 183179, 183180, 183181, 183182, 183183, 183184, 183186,
            183187, 183188, 183189, 183190, 183191, 183192, 183193, 183194,
            183195, 183196, 183197, 183198, 183199, 183200, 183201, 183202,
            183203, 183204, 183205, 183206, 183207, 183208, 183209, 183210,
            183211, 183212, 183213, 183214, 183215, 183216, 183217, 183218,
            183219, 183221, 183222, 183223, 183224, 183225, 183226, 183227,
            183228, 183229, 183230, 183231, 183232, 183233, 183234, 183235,
            183236, 183237, 183238, 183239, 183240, 183241, 183242, 183243,
            183244, 183245, 183246, 183247, 183248, 183249, 183250, 183251,
            183252, 183253, 183254, 183255, 183256, 183257, 183258, 183259,
            183260, 183261, 183262, 183263, 183264, 183265, 183266, 183267,
            183268, 183269, 183270, 183271, 183272, 183273, 183274, 183275,
            183276, 183277, 183278, 183279, 183280, 183281, 183282, 183283,
            183284, 183285, 183286, 183287, 183288, 183289, 183290, 183291,
            183292, 183293, 183294, 183295, 183296, 183297, 183298, 183299,
            183300, 183301, 183302, 183303, 183304, 183305, 183306, 183307,
            183308, 183309, 183310, 183311, 183312, 183313, 183314, 183315,
            183316, 183317, 183318, 183319, 183320, 183321, 183322, 183323,
            183324, 183325, 183326, 183327, 183328, 183329, 183330, 183331,
            183332, 183333, 183334, 183335, 183336, 183337, 183338, 183339,
            183340, 183341, 183342, 183343, 183344, 183345, 183346
        ];

        $columns = [
            'academic_requirement',
            'english_test_score',
            'other_test_score',
            'fees',
            'subject_area_and_level'
        ];

        $products = DB::table('products')->whereIn('id', $idsToFix)->get();

        foreach ($products as $product) {
            $updateData = [];

            foreach ($columns as $column) {
                $value = $product->{$column};

                if ($this->isDoubleEncodedJson($value)) {
                    $decodedValue = json_decode(json_decode($value, true), true);
                    $updateData[$column] = json_encode($decodedValue);
                }
            }

            if (!empty($updateData)) {
                DB::table('products')->where('id', $product->id)->update($updateData);
            }
        }

        $this->info('Double-encoded JSON data fixed successfully.');
    }

    private function isDoubleEncodedJson($value)
    {
        $decodedOnce = json_decode($value, true);

        if (is_string($decodedOnce)) {
            return true;
        }

        return false;
    }
}
