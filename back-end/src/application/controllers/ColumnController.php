<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\entities\ColumnEntity;
use app\services\ColumnService;
use JetBrains\PhpStorm\NoReturn;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;
use shared\utils\Checker;

class ColumnController
{
    public function __construct(
        private readonly Request       $request,
        private readonly Response      $response,
        private readonly ColumnService $columnService
    ) {
    }

    /**
     * @return null
     * @throws ResponseException
     */
    #[NoReturn] public function createColumn()
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data,
            [
                'title',
                'boardId',
            ], [
                'title'   => 'string',
                'boardId' => 'integer',
            ]
        );

        $user_id = $_SESSION['user_id'];
        $column_entity = new ColumnEntity();
        $column_entity->setBoardId($req_data['boardId']);
        $column_entity->setTitle($req_data['title']);

        $new_column = $this->columnService->handleCreateColumn($user_id, $column_entity);
        return $this->response->content(StatusCode::OK, "Create a new board successfully!", null, $new_column);
    }
}
