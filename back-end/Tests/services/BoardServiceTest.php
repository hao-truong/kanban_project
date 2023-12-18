<?php


namespace services;

use app\entities\BoardEntity;
use app\models\BoardModel;
use app\models\UserBoardModel;
use app\services\BoardService;
use app\services\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use shared\enums\ErrorMessage;
use shared\exceptions\ResponseException;

class BoardServiceTest extends TestCase
{
    private BoardService $boardService;
    private MockObject $boardModelMock;
    private MockObject $userServiceMock;
    private MockObject $userBoardModelMock;
    private static array $MATCHED_BOARD = [
        'id'         => 1,
        'title'      => 'Board title 1',
        'created at' => '2023-12-01 04:42:06.000',
        'updated_at' => '2023-12-01 04:42:06.000',
        'creator_id' => 1,
    ];

    protected function setUp(): void
    {
        $this->boardModelMock = $this->createMock(BoardModel::class);
        $this->userBoardModelMock = $this->createMock(UserBoardModel::class);
        $this->userServiceMock = $this->createMock(UserService::class);

        $this->boardService = new BoardService(
            $this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock
        );
    }

    public function testHandleUpdateBoard(): void
    {
        $this->boardService = $this->createPartialMock(
            BoardService::class, [
                'checkExistedBoard',
                'checkOwnerOfBoard'
            ]
        );
        $this->boardService->__construct($this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock);
        $this->boardService->expects($this->once())
                           ->method('checkExistedBoard')
                           ->willReturn(self::$MATCHED_BOARD)
        ;
        $this->boardService->expects($this->once())
                           ->method('checkOwnerOfBoard')
                           ->willReturn(true)
        ;

        $result_update = self::$MATCHED_BOARD;
        $result_update['title'] = 'Board 2';
        $result_update['updated_at'] = '2023-12-01 04:42:06.000';
        $this->boardModelMock->expects($this->once())
                             ->method('update')
                             ->willReturn($result_update)
        ;
        $board_entity = new BoardEntity();
        $board_entity->setTitle('Board 2');
        $board_entity->setId(1);

        $expect_result = self::$MATCHED_BOARD;
        $expect_result['title'] = 'Board 2';
        $expect_result['updated_at'] = '2023-12-01 04:42:06.000';

        $result = $this->boardService->handleUpdateBoard(1, $board_entity);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleCreateBoard(): void
    {
        $expect_result = self::$MATCHED_BOARD;
        $board_entity = new BoardEntity();
        $board_entity->setTitle('Board title 1');
        $board_entity->setCreatorId('1');

        $this->boardModelMock->expects($this->once())
                             ->method('save')
                             ->with(
                                 $board_entity->toArray()
                             )
                             ->willReturn(self::$MATCHED_BOARD)
        ;
        $this->userBoardModelMock->expects($this->once())->method('save');
        $result = $this->boardService->handleCreateBoard($board_entity);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleGetMyBoards(): void
    {
        $expect_result = [
            [
                'id'                => 67,
                'title'             => 'Board 1',
                'created_at'        => '2023-12-08 15:27:20.257',
                'updated_at'        => '2023-12-08 15:27:20.257',
                'creator_id'        => 25,
                'number_of_members' => 12
            ],
            [
                'id'                => 68,
                'title'             => 'Board 2',
                'created_at'        => '2023-12-08 15:27:20.257',
                'updated_at'        => '2023-12-08 15:27:20.257',
                'creator_id'        => 26,
                'number_of_members' => 10
            ]
        ];
        $this->boardService = $this->createPartialMock(
            BoardService::class, [
                'boardsWithNumberOfMembers',
            ]
        );
        $this->boardService->__construct($this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock);
        $this->boardService->expects($this->once())
                           ->method('boardsWithNumberOfMembers')
                           ->willReturn(
                               [
                                   [
                                       'id'                => 67,
                                       'title'             => 'Board 1',
                                       'created_at'        => '2023-12-08 15:27:20.257',
                                       'updated_at'        => '2023-12-08 15:27:20.257',
                                       'creator_id'        => 25,
                                       'number_of_members' => 12
                                   ],
                                   [
                                       'id'                => 68,
                                       'title'             => 'Board 2',
                                       'created_at'        => '2023-12-08 15:27:20.257',
                                       'updated_at'        => '2023-12-08 15:27:20.257',
                                       'creator_id'        => 26,
                                       'number_of_members' => 10
                                   ]
                               ]
                           )
        ;
        $this->userBoardModelMock
            ->expects($this->once())
            ->method('join')
            ->willReturn(
                [
                    [
                        'id'         => 67,
                        'title'      => 'Board 1',
                        'created_at' => '2023-12-08 15:27:20.257',
                        'updated_at' => '2023-12-08 15:27:20.257',
                        'creator_id' => 25,
                    ],
                    [
                        'id'         => 68,
                        'title'      => 'Board 2',
                        'created_at' => '2023-12-08 15:27:20.257',
                        'updated_at' => '2023-12-08 15:27:20.257',
                        'creator_id' => 26,
                    ]
                ]
            )
        ;
        $result = $this->boardService->handleGetMyBoards(1);
        $this->assertEquals($expect_result, $result);
    }

    public function testBoardsWithNumberOfMembers(): void
    {
        $expect_result = [
            [
                'id'                => 67,
                'title'             => 'Board 1',
                'created_at'        => '2023-12-08 15:27:20.257',
                'updated_at'        => '2023-12-08 15:27:20.257',
                'creator_id'        => 25,
                'number_of_members' => 3,
            ],
            [
                'id'                => 68,
                'title'             => 'Board 2',
                'created_at'        => '2023-12-08 15:27:20.257',
                'updated_at'        => '2023-12-08 15:27:20.257',
                'creator_id'        => 26,
                'number_of_members' => 2,
            ]
        ];
        $matchers = $this->exactly(2);
        $this->userBoardModelMock
            ->expects($matchers)
            ->method('count')
            ->willReturn(3, 2)
        ;
        $boards = [
            [
                'id'         => 67,
                'title'      => 'Board 1',
                'created_at' => '2023-12-08 15:27:20.257',
                'updated_at' => '2023-12-08 15:27:20.257',
                'creator_id' => 25,
            ],
            [
                'id'         => 68,
                'title'      => 'Board 2',
                'created_at' => '2023-12-08 15:27:20.257',
                'updated_at' => '2023-12-08 15:27:20.257',
                'creator_id' => 26,
            ]
        ];
        $result = $this->boardService->boardsWithNumberOfMembers($boards);
        $this->assertEquals($expect_result, $result);
    }

    public function testCheckExistedBoardFail(): void
    {
        $this->boardModelMock->expects($this->once())
                             ->method('findOne')
                             ->willReturn(null)
        ;

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::BOARD_NOT_FOUND->value);

        $this->boardService->checkExistedBoard(1);
    }

    public function testCheckExistedBoardSuccess(): void
    {
        $this->boardModelMock->expects($this->once())
                             ->method('findOne')
                             ->willReturn(self::$MATCHED_BOARD)
        ;

        $expect_result = self::$MATCHED_BOARD;
        $result = $this->boardService->checkExistedBoard(1);
        $this->assertEquals($expect_result, $result);
    }

    public function testCheckMemberOfBoardFail(): void
    {
        $this->userBoardModelMock->expects($this->once())
                                 ->method('findOne')
                                 ->willReturn(null)
        ;

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::NOT_BOARD_MEMBER->value);
        $this->boardService->checkMemberOfBoard(1, 1);
    }

    public function testCheckMemberOfBoardSuccess(): void
    {
        $this->userBoardModelMock->expects($this->once())
                                 ->method('findOne')
                                 ->willReturn(
                                     [
                                         'user_id'  => 1,
                                         'board_id' => 1
                                     ]
                                 )
        ;

        $expect_result = true;
        $result = $this->boardService->checkMemberOfBoard(1, 1);
        $this->assertEquals($expect_result, $result);
    }

    public function testCheckOwnerOfBoardFail(): void
    {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::NOT_BOARD_OWNER->value);

        $this->boardService->checkOwnerOfBoard(2, self::$MATCHED_BOARD);
    }

    public function testCheckOwnerOfBoardSuccess(): void
    {
        $expect_result = true;
        $result = $this->boardService->checkOwnerOfBoard(1, self::$MATCHED_BOARD);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleDeleteBoard(): void
    {
        $this->boardService = $this->createPartialMock(
            BoardService::class, [
                'checkExistedBoard',
                'checkOwnerOfBoard'
            ]
        );
        $this->boardService->__construct($this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock);
        $this->boardService->expects($this->once())
                           ->method('checkExistedBoard')
                           ->willReturn(self::$MATCHED_BOARD)
        ;
        $this->boardService->expects($this->once())
                           ->method('checkOwnerOfBoard')
                           ->willReturn(true)
        ;
        $this->boardModelMock->expects($this->once())
                             ->method('deleteById')
        ;
        $this->boardService->handleDeleteBoard(1, 1);
    }

    public function testHandleGetBoard(): void
    {
        $this->boardService = $this->createPartialMock(
            BoardService::class, [
                'checkExistedBoard',
                'checkMemberOfBoard'
            ]
        );
        $this->boardService->__construct($this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock);
        $this->boardService->expects($this->once())
                           ->method('checkExistedBoard')
                           ->willReturn(self::$MATCHED_BOARD)
        ;
        $this->boardService->expects($this->once())
                           ->method('checkMemberOfBoard')
                           ->willReturn(true)
        ;
        $expect_result = self::$MATCHED_BOARD;
        $result = $this->boardService->handleGetBoard(1, 1,);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleAddMemberToBoardFail(): void
    {
        $this->boardService = $this->createPartialMock(
            BoardService::class, [
                'checkExistedBoard',
                'checkOwnerOfBoard',
            ]
        );
        $this->boardService->__construct($this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock);
        $this->boardService->expects($this->once())
                           ->method('checkExistedBoard')
                           ->willReturn(self::$MATCHED_BOARD)
        ;
        $this->userServiceMock->expects($this->once())
                              ->method('getUserByUsername')
                              ->willReturn(
                                  [
                                      'id' => 1
                                  ]
                              )
        ;
        $this->userBoardModelMock->expects($this->once())
                                 ->method('findOne')
                                 ->willReturn(
                                     [
                                         'user_id'  => 1,
                                         'board_id' => 1,
                                     ]
                                 )
        ;

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::JOINED_MEMBER_BOARD->value);
        $this->boardService->handleAddMemberToBoard(1, 1, 'haotruong');
    }

    public function testHandleAddMemberToBoardSuccess(): void
    {
        $this->boardService = $this->createPartialMock(
            BoardService::class, [
                'checkExistedBoard',
                'checkOwnerOfBoard',
            ]
        );
        $this->boardService->__construct($this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock);
        $this->boardService->expects($this->once())
                           ->method('checkExistedBoard')
                           ->willReturn(self::$MATCHED_BOARD)
        ;
        $this->userServiceMock->expects($this->once())
                              ->method('getUserByUsername')
                              ->willReturn(
                                  [
                                      'id' => 1
                                  ]
                              )
        ;
        $this->userBoardModelMock->expects($this->once())
                                 ->method('findOne')
                                 ->willReturn(
                                     null
                                 )
        ;
        $this->userBoardModelMock->expects($this->once())
                                 ->method('save')
        ;

        $this->boardService->handleAddMemberToBoard(1, 1, 'haotruong');
    }

    public function testHandleLeaveBoard(): void
    {
        $this->boardService = $this->createPartialMock(
            BoardService::class, [
                'checkExistedBoard',
                'checkMemberOfBoard',
            ]
        );
        $this->boardService->__construct($this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock);
        $this->boardService->expects($this->once())
                           ->method('checkExistedBoard')
                           ->willReturn(self::$MATCHED_BOARD)
        ;
        $this->boardService->expects($this->once())
                           ->method('checkMemberOfBoard')
                           ->willReturn(true)
        ;
        $this->userBoardModelMock->expects($this->once())
                                 ->method('deleteById')
        ;
        $this->boardService->handleLeaveBoard(1, 1);
    }

    public function testHandleGetMembersOfBoard(): void
    {
        $this->boardService = $this->createPartialMock(
            BoardService::class, [
                'checkExistedBoard',
                'checkMemberOfBoard',
            ]
        );
        $this->boardService->__construct($this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock);
        $this->boardService->expects($this->once())
                           ->method('checkExistedBoard')
                           ->willReturn(self::$MATCHED_BOARD)
        ;
        $this->boardService->expects($this->once())
                           ->method('checkMemberOfBoard')
                           ->willReturn(true)
        ;
        $this->userBoardModelMock->expects($this->once())
                                 ->method('join')
                                 ->willReturn(
                                     [
                                         [
                                             'id'       => 1,
                                             'username' => 'haotruong',
                                             'alias'    => 'Truong Van Hao',
                                             'email'    => 'truongvanhao159@gmail.com'
                                         ],
                                         [
                                             'id'       => 2,
                                             'username' => 'haotruong123',
                                             'alias'    => 'Truong Van Hao',
                                             'email'    => 'truongvanhao159@gmail.com'
                                         ]
                                     ]
                                 )
        ;
        $expect_result = [
            [
                'id'       => 1,
                'username' => 'haotruong',
                'alias'    => 'Truong Van Hao',
                'email'    => 'truongvanhao159@gmail.com'
            ],
            [
                'id'       => 2,
                'username' => 'haotruong123',
                'alias'    => 'Truong Van Hao',
                'email'    => 'truongvanhao159@gmail.com'
            ]
        ];
        $result = $this->boardService->handleGetMembersOfBoard(1, 1);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleSearchByTitle(): void
    {
        $this->boardService = $this->createPartialMock(
            BoardService::class, [
                'boardsWithNumberOfMembers',
            ]
        );
        $this->boardService->__construct($this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock);
        $this->boardService->expects($this->once())
                           ->method('boardsWithNumberOfMembers')
                           ->willReturn(
                               [
                                   [
                                       'id'                => 67,
                                       'title'             => 'Board 1',
                                       'created_at'        => '2023-12-08 15:27:20.257',
                                       'updated_at'        => '2023-12-08 15:27:20.257',
                                       'creator_id'        => 25,
                                       'number_of_members' => 12
                                   ],
                                   [
                                       'id'                => 68,
                                       'title'             => 'Board 2',
                                       'created_at'        => '2023-12-08 15:27:20.257',
                                       'updated_at'        => '2023-12-08 15:27:20.257',
                                       'creator_id'        => 26,
                                       'number_of_members' => 10
                                   ]
                               ]
                           )
        ;
        $this->boardModelMock->expects($this->once())
                             ->method('search')
                             ->willReturn(
                                 [
                                     [
                                         'id'         => 67,
                                         'title'      => 'Board 1',
                                         'created_at' => '2023-12-08 15:27:20.257',
                                         'updated_at' => '2023-12-08 15:27:20.257',
                                         'creator_id' => 25,
                                     ],
                                     [
                                         'id'         => 68,
                                         'title'      => 'Board 2',
                                         'created_at' => '2023-12-08 15:27:20.257',
                                         'updated_at' => '2023-12-08 15:27:20.257',
                                         'creator_id' => 26,
                                     ]
                                 ]
                             )
        ;
        $expect_result = [
            [
                'id'                => 67,
                'title'             => 'Board 1',
                'created_at'        => '2023-12-08 15:27:20.257',
                'updated_at'        => '2023-12-08 15:27:20.257',
                'creator_id'        => 25,
                'number_of_members' => 12
            ],
            [
                'id'                => 68,
                'title'             => 'Board 2',
                'created_at'        => '2023-12-08 15:27:20.257',
                'updated_at'        => '2023-12-08 15:27:20.257',
                'creator_id'        => 26,
                'number_of_members' => 10
            ]
        ];
        $result = $this->boardService->handleSearchByTitle(1, 'board');
        $this->assertEquals($expect_result, $result);
    }
}
