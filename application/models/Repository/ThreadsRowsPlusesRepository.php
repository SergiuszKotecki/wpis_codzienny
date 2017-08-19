<?php
namespace app\models\Repository;
use app\models\ThreadsRowsPluses; 

class ThreadsRowsPlusesRepository
{
    public function getPlusesIn24h($threadRowId)
    {
        $threadRowPluses = ThreadsRowsPluses::findAll(['thread_row_id' => $threadRowId]);

        $return[] = [
            ['f' => 'Godzina', 'type'=>'string'],
            ['f' => 'Plusy', 'type'=>'number']
        ];
        foreach ($threadRowPluses as $row) {
            $return[] = [$this->int2hour($row->hour), $row->pluses];
        }

        return $return;
    }

    private function int2hour($hour)
    {
        return (($hour > 9) ? $hour : '0'.$hour) . ':00:00';
    }
}