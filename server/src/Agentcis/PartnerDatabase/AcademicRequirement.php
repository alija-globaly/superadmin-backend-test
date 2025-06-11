<?php

namespace Agentcis\PartnerDatabase;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

final class AcademicRequirement implements Jsonable, Arrayable
{
    public $degreeLevel;
    public $academicScoreType;
    public $academicScore;

    /**
     * AcademicRequirement constructor.
     * @param $degreeLevel
     * @param $academicScoreType
     * @param $academicScore
     */
    private function __construct($degreeLevel, $academicScoreType, $academicScore)
    {
        $this->degreeLevel = $degreeLevel;
        $this->academicScoreType = $academicScoreType;
        $this->academicScore = $academicScore;
    }

    public static function fromString($degreeLevel, $academicScoreType, $academicScore)
    {
        // TODO add validation and set attributes
        return new self($degreeLevel, $academicScoreType, $academicScore);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return \json_encode($this->toArray());
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'degree_level' => $this->degreeLevel,
            'academic_score_type' => $this->academicScoreType,
            'academic_score' => $this->academicScore,
        ];
    }
}
