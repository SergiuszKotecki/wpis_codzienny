<?php
namespace app\models\Repository;
use app\models\ThreadsRowsCommentsCnt;

class ThreadsRowsCommentsCntRepository
{
    public function getCommentsIn24h($threadRowId)
    {
        $threadRowCommentsCnt = ThreadsRowsCommentsCnt::findAll(['thread_row_id' => $threadRowId]);

        $return[] = [
            ['f' => 'Godzina', 'type'=>'string'],
            ['f' => 'Komentarzy', 'type'=>'number']
        ];
        foreach ($threadRowCommentsCnt as $row) {
            $return[] = [$this->int2hour($row->hour), $row->cnt];
        }

        return $return;
    }

    private function int2hour($hour)
    {
        return (($hour > 9) ? $hour : '0'.$hour) . ':00:00';
    }
}