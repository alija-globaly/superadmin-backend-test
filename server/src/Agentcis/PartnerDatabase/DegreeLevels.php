<?php
/**
 * Created by PhpStorm.
 * User: amrit
 * Date: 8/26/19
 * Time: 4:19 PM
 */

namespace Agentcis\PartnerDatabase;

class DegreeLevels
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            [ 'id'=> 6, 'name'=> "Advance Diploma" ],
            [ 'id'=> 7, 'name'=> "Bachelor" ],
            [ 'id'=> 4, 'name'=> "Certificate" ],
            [ 'id'=> 5, 'name'=> "Diploma" ],
            [ 'id'=> 8, 'name'=> "Graduate Diploma" ],
            [ 'id'=> 3, 'name'=> "High School" ],
            [ 'id'=> 9, 'name'=> "Master" ],
            [ 'id'=> 10, 'name'=> "Master (Research)" ],
            [ 'id'=> 1, 'name'=> "Non AQF Award" ],
            [ 'id'=> 11, 'name'=> "PHD" ],
            [ 'id'=> 2, 'name'=> "School" ]
        ];
    }
}
