<?php
declare(strict_types=1);

namespace app\services;

use app\models\ColumnModel;
use shared\utils\Checker;

class  ColumnService {
    public function __construct(
        private readonly ColumnModel $columnModel
    ) { }
}
