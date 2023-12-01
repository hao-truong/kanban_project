<?php
declare(strict_types=1);

namespace app\services;

use app\models\ColumnModel;
use shared\utils\Checker;

class  ColumnService {
    public function __construct(
        private ColumnModel $columnModel
    ) { }

    public function handleCreateColumn(array $req_data) {
        Checker::checkMissingFields(
            [
                'title',
                'boardId'
            ], $req_data
        );

        $user_id = $_SESSION['user_id'];
    }
}
