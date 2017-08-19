<?php
namespace app\models\Enum;

class ThreadsRowsEnum
{
    /**
     * Statuses
     */
    const STATUS_NEW = 0;
    const STATUS_READY_TO_SEND = 1;
    const STATUS_SEND = 2;
    const STATUS_DELETED = 3;
    const STATUS_SEND_ERROR = 4;

    /**
     *
     * @return array
     */
    public function getAll()
    {
        return [
            self::STATUS_NEW                => 'Nieaktywny',
            self::STATUS_READY_TO_SEND      => 'Aktywny',
            self::STATUS_SEND               => 'Wysłany',
            self::STATUS_DELETED            => 'Usunięty',
            self::STATUS_SEND_ERROR         => 'Nieudane wysłanie',
        ];
    }

    /**
     *
     * @return array
     */
    public function getBegining()
    {
        return [
            self::STATUS_READY_TO_SEND      => 'Aktywny',
            self::STATUS_NEW                => 'Nieaktywny',
        ];
    }

    /**
     *
     * @return array
     */
    public function getRowStyles()
    {
        return [
            self::STATUS_NEW                => '',
            self::STATUS_READY_TO_SEND      => 'color: grey;',
            self::STATUS_SEND               => 'color: grey; background: rgba(4, 255, 0, 0.08)',
            self::STATUS_DELETED            => 'color: grey; text-decoration: line-through',
            self::STATUS_SEND_ERROR         => 'color: grey; background: rgba(255, 0, 0, 0.08)',
        ];
    }
}