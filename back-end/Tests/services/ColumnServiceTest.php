<?php

namespace services;

use app\entities\ColumnEntity;
use app\models\ColumnModel;
use app\services\ColumnService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use shared\enums\ErrorMessage;
use shared\exceptions\ResponseException;

class ColumnServiceTest extends TestCase
{
    private ColumnService $columnService;
    private MockObject $columnModel;
    private static array $MATCHED_COLUMN = [
        'id'         => 1,
        'created_at' => "2023-12-15 10:19:21.482",
        'updated_at' => "2023-12-15 10:19:21.482",
        'creator_id' => 1,
        'position'   => 3,
        'title'      => 'Title column 1',
        'board_id'   => 1
    ];

    protected function setUp(): void
    {
        $this->columnModel = $this->createMock(ColumnModel::class);
        $this->columnService = new ColumnService($this->columnModel);
    }

    public function testHandleCreateColumn(): void
    {
        $this->columnModel->expects($this->once())
                          ->method('count')
                          ->willReturn(2)
        ;
        $this->columnModel->expects($this->once())
                          ->method('save')
                          ->willReturn(self::$MATCHED_COLUMN)
        ;

        $column_entity = new ColumnEntity();
        $column_entity->setBoardId(1);
        $column_entity->setTitle('Title column 1');
        $column_entity->setCreatorId(1);

        $expect_result = self::$MATCHED_COLUMN;
        $result = $this->columnService->handleCreateColumn($column_entity);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleGetColumnsOfBoard(): void
    {
        $this->columnModel->expects($this->once())
                          ->method('find')
                          ->willReturn(
                              [
                                  [
                                      'id'         => 1,
                                      'created_at' => "2023-12-15 10:19:21.482",
                                      'updated_at' => "2023-12-15 10:19:21.482",
                                      'creator_id' => 1,
                                      'position'   => 3,
                                      'title'      => 'Title column 1',
                                      'board_id'   => 1
                                  ],
                                  [
                                      'id'         => 2,
                                      'created_at' => "2023-12-15 10:19:21.482",
                                      'updated_at' => "2023-12-15 10:19:21.482",
                                      'creator_id' => 1,
                                      'position'   => 4,
                                      'title'      => 'Title column 1',
                                      'board_id'   => 1
                                  ]
                              ]
                          )
        ;
        $expect_result = [
            [
                'id'         => 1,
                'created_at' => "2023-12-15 10:19:21.482",
                'updated_at' => "2023-12-15 10:19:21.482",
                'creator_id' => 1,
                'position'   => 3,
                'title'      => 'Title column 1',
                'board_id'   => 1
            ],
            [
                'id'         => 2,
                'created_at' => "2023-12-15 10:19:21.482",
                'updated_at' => "2023-12-15 10:19:21.482",
                'creator_id' => 1,
                'position'   => 4,
                'title'      => 'Title column 1',
                'board_id'   => 1
            ]
        ];
        $result = $this->columnService->handleGetColumnsOfBoard(1);
        $this->assertEquals($expect_result, $result);
    }

    public function testCheckExistedColumnFail(): void
    {
        $this->columnModel->expects($this->once())
                          ->method('findOne')
                          ->willReturn(null)
        ;

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::COLUMN_NOT_FOUND->value);

        $this->columnService->checkExistedColumn(1);
    }

    public function testCheckExistedColumnSuccess(): void
    {
        $this->columnModel->expects($this->once())
                          ->method('findOne')
                          ->willReturn(self::$MATCHED_COLUMN)
        ;
        $expect_result = self::$MATCHED_COLUMN;
        $result = $this->columnService->checkExistedColumn(1);
        $this->assertEquals($expect_result, $result);
    }

    public function testCheckColumnInBoardFail(): void
    {
        $this->columnService = $this->createPartialMock(
            ColumnService::class, [
            'checkExistedColumn'
        ]
        );
        $this->columnService->__construct($this->columnModel);
        $this->columnService->expects($this->once())
                            ->method('checkExistedColumn')
                            ->willReturn(self::$MATCHED_COLUMN)
        ;
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::COLUMN_NOT_IN_BOARD->value);
        $this->columnService->checkColumnInBoard(1, 2);
    }

    public function testCheckColumnInBoardSuccess(): void
    {
        $this->columnService = $this->createPartialMock(
            ColumnService::class, [
            'checkExistedColumn'
        ]
        );
        $this->columnService->__construct($this->columnModel);
        $this->columnService->expects($this->once())
                            ->method('checkExistedColumn')
                            ->willReturn(self::$MATCHED_COLUMN)
        ;
        $expect_result = self::$MATCHED_COLUMN;
        $result = $this->columnService->checkColumnInBoard(1, 1);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleUpdateColumn(): void
    {
        $this->columnService = $this->createPartialMock(
            ColumnService::class, [
            'checkColumnInBoard'
        ]
        );
        $this->columnService->__construct($this->columnModel);
        $this->columnService->expects($this->once())
                            ->method('checkColumnInBoard')
                            ->willReturn(self::$MATCHED_COLUMN)
        ;
        $this->columnModel->expects($this->once())
                          ->method('update')
                          ->willReturn(
                              [
                                  'id'         => 1,
                                  'created_at' => "2023-12-15 10:19:21.482",
                                  'updated_at' => "2023-12-15 10:19:21.482",
                                  'creator_id' => 1,
                                  'position'   => 3,
                                  'title'      => 'Title column updated',
                                  'board_id'   => 1
                              ]
                          )
        ;

        $column_entity = new ColumnEntity();
        $column_entity->setId(1);
        $column_entity->setTitle('Title column updated');
        $column_entity->setBoardId(1);

        $expect_result = [
            'id'         => 1,
            'created_at' => "2023-12-15 10:19:21.482",
            'updated_at' => "2023-12-15 10:19:21.482",
            'creator_id' => 1,
            'position'   => 3,
            'title'      => 'Title column updated',
            'board_id'   => 1
        ];
        $result = $this->columnService->handleUpdateColumn($column_entity);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleDeleteColumn(): void
    {
        $this->columnService = $this->createPartialMock(
            ColumnService::class, [
            'checkColumnInBoard'
        ]
        );
        $this->columnService->__construct($this->columnModel);
        $this->columnService->expects($this->once())
                            ->method('checkColumnInBoard')
        ;
        $this->columnModel->expects($this->once())
                          ->method('deleteById')
        ;
        $this->columnService->handleDeleteColumn(1, 1);
    }

    public function testHandleSwapPositionOfCoupleColumn(): void {
        $matchers = $this->exactly(2);
        $this->columnModel->expects($matchers)
            ->method('update');

        $column_first = ColumnEntity::fromArray(self::$MATCHED_COLUMN);
        $column_second = ColumnEntity::fromArray(self::$MATCHED_COLUMN);
        $column_second->setId(2);
        $column_second->setPosition(5);
        $this->columnService->handleSwapPositionOfCoupleColumn($column_first, $column_second);
    }
}
