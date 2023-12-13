<?php


use app\entities\BoardEntity;
use app\models\BoardModel;
use app\models\UserBoardModel;
use app\models\UserModel;
use app\services\BoardService;
use app\services\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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

        $this->boardService = new BoardService($this->boardModelMock, $this->userServiceMock, $this->userBoardModelMock);
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
        $this->userBoardModelMock
            ->expects($this->once())
            ->method('join')
            ->with(
                [
                    'table'     => 'boards',
                    'as'        => 'b',
                    'condition' => [
                        'board_id',
                        'id'
                    ],
                    'select'    => [
                        'id',
                        'title',
                        'created_at',
                        'updated_at',
                        'creator_id',
                    ],
                ],
                [
                    'where' => [
                        'user_id',
                        1,
                    ]
                ]
            )
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
        $matchers = $this->exactly(2);
        $this->userBoardModelMock
            ->expects($matchers)
            ->method('count')
            ->willReturnCallback(
                function (string $field, int $board_id) use ($matchers) {
                    match ([
                        $field,
                        $board_id
                    ]) {
                        [
                            'board_id',
                            67
                        ] => 3,
                        [
                            'board_id',
                            68
                        ] => 2
                    };
                }
            )
            ->willReturn(3, 2)
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
            ->willReturnCallback(
                function (string $field, int $board_id) use ($matchers) {
                    match ([
                        $field,
                        $board_id
                    ]) {
                        [
                            'board_id',
                            67
                        ] => 3,
                        [
                            'board_id',
                            68
                        ] => 2
                    };
                }
            )
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
}
