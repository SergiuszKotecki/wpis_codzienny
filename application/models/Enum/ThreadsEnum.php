<?php
namespace app\models\Enum;

class ThreadsEnum
{
    /**
     * Statuses
     */
    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    private $_labels = [
        self::STATUS_UNACTIVE   => 'Nieaktywny',
        self::STATUS_ACTIVE     => 'Aktywny',
        self::STATUS_DELETED    => 'Usunięty',
    ];

    /**
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_labels;
    }

    /**
     *
     * @return array
     */
    public function getBegining()
    {
        return [
            self::STATUS_ACTIVE        => $this->_labels[self::STATUS_ACTIVE],
            self::STATUS_UNACTIVE      => $this->_labels[self::STATUS_UNACTIVE],
        ];
    }
}