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

    public function testHandleCreateBoardSuccess(): void
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
}
