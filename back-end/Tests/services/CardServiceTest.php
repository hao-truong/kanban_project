<?php

namespace services;

use app\entities\CardEntity;
use app\models\CardModel;
use app\services\CardService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use shared\enums\ErrorMessage;
use shared\exceptions\ResponseException;
use shared\utils\Converter;

class CardServiceTest extends TestCase
{
    private CardService $cardService;
    private MockObject $cardModel;
    private MockObject $converter;
    private static array $MATCHED_CARD = [
        'assigned_user' => 1,
        'column_id'     => 1,
        'created_at'    => '2023-12-11 22:28:33.019',
        'updated_at'    => '2023-12-11 22:28:33.019',
        'description'   => 'Abc description',
        'id'            => 1,
        'position'      => 1,
        'status'        => "1",
        'title'         => 'Card 1 title',
    ];

    public function setUp(): void
    {
        $this->cardModel = $this->createMock(CardModel::class);
        $this->converter = $this->createMock(Converter::class);
        $this->cardService = new CardService($this->cardModel, $this->converter);
    }

    public function testExistedCardFail(): void
    {
        $this->cardModel->expects($this->once())
                        ->method('findOne')
                        ->willReturn(null)
        ;
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::CARD_NOT_FOUND->value);
        $this->cardService->checkExistedCard(1);
    }

    public function testExistedCardSuccess(): void
    {
        $this->cardModel->expects($this->once())
                        ->method('findOne')
                        ->willReturn(self::$MATCHED_CARD)
        ;
        $expect_result = self::$MATCHED_CARD;
        $result = $this->cardService->checkExistedCard(1);
        $this->assertEquals($expect_result, $result);
    }

    public function testCheckCardInColumnFail(): void
    {
        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkExistedCard']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->once())
                          ->method('checkExistedCard')
                          ->willReturn(self::$MATCHED_CARD)
        ;

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::CARD_NOT_IN_COLUMN->value);
        $this->cardService->checkCardInColumn(2, 1);
    }

    public function testCheckCardInColumnSuccess(): void
    {
        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkExistedCard']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->once())
                          ->method('checkExistedCard')
                          ->willReturn(self::$MATCHED_CARD)
        ;

        $expect_result = self::$MATCHED_CARD;
        $result = $this->cardService->checkCardInColumn(1, 1);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleUpdateTitleCard(): void
    {
        $expect_result = [
            'assigned_user' => null,
            'column_id'     => 1,
            'created_at'    => '2023-12-11 22:28:33.019',
            'updated_at'    => '2023-12-11 22:28:33.019',
            'description'   => 'Abc description',
            'id'            => 1,
            'position'      => 1,
            'status'        => "1",
            'title'         => 'Card 1 title updated',
        ];
        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkCardInColumn']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->once())
                          ->method('checkCardInColumn')
                          ->willReturn(self::$MATCHED_CARD)
        ;
        $this->cardModel->expects($this->once())
                        ->method('update')
                        ->willReturn($expect_result)
        ;
        $card_entity = new CardEntity();
        $card_entity->setTitle('Card 1 title updated');
        $card_entity->setColumnId(1);
        $card_entity->setId(1);
        $card_entity->setTitle('Card 1 title updated');

        $result = $this->cardService->handleUpdateTitleCard($card_entity);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleDeleteCard(): void
    {
        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkExistedCard']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->once())
                          ->method('checkExistedCard')
        ;
        $this->cardModel->expects($this->once())
                        ->method('deleteById')
        ;
        $this->cardService->handleDeleteCard(1);
    }

    public function testCheckAssignedUserOfCardFail(): void
    {
        $this->cardModel->expects($this->once())
                        ->method('findOne')
                        ->willReturn(self::$MATCHED_CARD)
        ;
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::USER_WAS_ASSIGNED_USER_OF_THIS_CARD->value);
        $this->cardService->checkAssignedUserOfCard(1, 1);
    }

    public function testCheckAssignedUserOfCardSuccess(): void
    {
        $this->cardModel->expects($this->once())
                        ->method('findOne')
                        ->willReturn(self::$MATCHED_CARD)
        ;
        $expect_result = self::$MATCHED_CARD;
        $result = $this->cardService->checkAssignedUserOfCard(2, 1);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleAssignMemberToBoard(): void
    {
        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkAssignedUserOfCard']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->once())
                          ->method('checkAssignedUserOfCard')
                          ->willReturn(self::$MATCHED_CARD)
        ;
        $this->cardModel->expects($this->once())
                        ->method('update')
        ;
        $this->cardService->handleAssignMemberToBoard(2, 1);
    }

    public function testHandleCreateCardColumnWithEmptyCard(): void
    {
        $this->cardModel->expects($this->once())
                        ->method('find')
                        ->willReturn(
                            [
                                [
                                    'assigned_user' => 1,
                                    'column_id'     => 1,
                                    'created_at'    => '2023-12-11 22:28:33.019',
                                    'updated_at'    => '2023-12-11 22:28:33.019',
                                    'description'   => 'Abc description',
                                    'id'            => 1,
                                    'position'      => 1,
                                    'status'        => "1",
                                    'title'         => 'Card 1 title',
                                ],
                                [
                                    'assigned_user' => 1,
                                    'column_id'     => 1,
                                    'created_at'    => '2023-12-11 22:28:33.019',
                                    'updated_at'    => '2023-12-11 22:28:33.019',
                                    'description'   => 'Abc description',
                                    'id'            => 2,
                                    'position'      => 2,
                                    'status'        => "1",
                                    'title'         => 'Card 2 title',
                                ]
                            ]
                        )
        ;
        $this->cardModel->expects($this->once())
                        ->method('save')
                        ->willReturn(
                            [
                                'assigned_user' => 1,
                                'column_id'     => 1,
                                'created_at'    => '2023-12-11 22:28:33.019',
                                'updated_at'    => '2023-12-11 22:28:33.019',
                                'description'   => 'Abc description',
                                'id'            => 3,
                                'position'      => 3,
                                'status'        => "1",
                                'title'         => 'Card 3 title',
                            ]
                        )
        ;
        $expect_result = [
            'assigned_user' => 1,
            'column_id'     => 1,
            'created_at'    => '2023-12-11 22:28:33.019',
            'updated_at'    => '2023-12-11 22:28:33.019',
            'description'   => 'Abc description',
            'id'            => 3,
            'position'      => 3,
            'status'        => "1",
            'title'         => 'Card 3 title',
        ];
        $card_entity = new CardEntity();
        $card_entity->setColumnId(1);
        $card_entity->setTitle('Card 3 title');
        $result = $this->cardService->handleCreateCard($card_entity);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleCreateColumnWithCards(): void
    {
        $this->cardModel->expects($this->once())
                        ->method('find')
                        ->willReturn([])
        ;
        $this->cardModel->expects($this->once())
                        ->method('save')
                        ->willReturn(
                            [
                                'assigned_user' => 1,
                                'column_id'     => 1,
                                'created_at'    => '2023-12-11 22:28:33.019',
                                'updated_at'    => '2023-12-11 22:28:33.019',
                                'description'   => 'Abc description',
                                'id'            => 1,
                                'position'      => 1,
                                'status'        => "1",
                                'title'         => 'Card 1 title',
                            ]
                        )
        ;
        $expect_result = [
            'assigned_user' => 1,
            'column_id'     => 1,
            'created_at'    => '2023-12-11 22:28:33.019',
            'updated_at'    => '2023-12-11 22:28:33.019',
            'description'   => 'Abc description',
            'id'            => 1,
            'position'      => 1,
            'status'        => "1",
            'title'         => 'Card 1 title',
        ];
        $card_entity = new CardEntity();
        $card_entity->setColumnId(1);
        $card_entity->setTitle('Card 1 title');
        $result = $this->cardService->handleCreateCard($card_entity);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleGetDetailCard(): void
    {
        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkExistedCard']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->once())
                          ->method('checkExistedCard')
        ;
        $this->cardModel->expects($this->once())
                        ->method('join')
                        ->willReturn([
                            [
                                "id"                     => 1,
                                "column_id"              => 65,
                                "title"                  => "card 2",
                                "description"            => "<p>Hello world<\/p><p><br><\/p>",
                                "created_at"             => "2023-12-11 15:42:22.095",
                                "updated_at"             => "2023-12-11 15:42:22.095",
                                "status"                 => "1",
                                "assigned_user"          => 1,
                                "assigned_user_id"       => 1,
                                "assigned_user_username" => "haotruong",
                                "assigned_user_alias"    => "Truong Van Hao",
                                "assigned_user_email"    => "truongvanhao159@gmail.com",
                                "position"               => 1
                            ]
                        ])
        ;
        $this->converter->expects($this->once())
                        ->method('toCardResponse')
                        ->willReturn(
                            [
                                "id"            => 1,
                                "column_id"     => 65,
                                "title"         => "card 2",
                                "description"   => "<p>Hello world<\/p><p><br><\/p>",
                                "created_at"    => "2023-12-11 15:42:22.095",
                                "updated_at"    => "2023-12-11 15:42:22.095",
                                "status"        => "1",
                                "assigned_user" => [
                                    "id"       => 25,
                                    "username" => "haotruong",
                                    "alias"    => "Truong Van Hao",
                                    "email"    => "truongvanhao159@gmail.com"
                                ],
                                "position"      => 1
                            ]
                        )
        ;
        $expect_result = [
            "id"            => 1,
            "column_id"     => 65,
            "title"         => "card 2",
            "description"   => "<p>Hello world<\/p><p><br><\/p>",
            "created_at"    => "2023-12-11 15:42:22.095",
            "updated_at"    => "2023-12-11 15:42:22.095",
            "status"        => "1",
            "assigned_user" => [
                "id"       => 25,
                "username" => "haotruong",
                "alias"    => "Truong Van Hao",
                "email"    => "truongvanhao159@gmail.com"
            ],
            "position"      => 1
        ];
        $result = $this->cardService->handleGetDetailCard(1);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleChangeColumnWithEmptyCard(): void
    {
        $expect_result = self::$MATCHED_CARD;
        $expect_result['position'] = 1;
        $expect_result['column_id'] = 2;
        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkExistedCard']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->once())
                          ->method('checkExistedCard')
                          ->willReturn(self::$MATCHED_CARD)
        ;
        $this->cardModel->expects($this->once())
                        ->method('find')
                        ->willReturn([])
        ;

        $this->cardModel->expects($this->once())
                        ->method('update')
                        ->willReturn($expect_result)
        ;
        $result = $this->cardService->handleChangeColumn(1, 2);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleChangeColumnWithCards(): void
    {
        $expect_result = self::$MATCHED_CARD;
        $expect_result['position'] = 3;
        $expect_result['column_id'] = 2;
        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkExistedCard']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->once())
                          ->method('checkExistedCard')
                          ->willReturn(self::$MATCHED_CARD)
        ;
        $this->cardModel->expects($this->once())
                        ->method('find')
                        ->willReturn([
                            self::$MATCHED_CARD,
                            self::$MATCHED_CARD
                        ])
        ;

        $this->cardModel->expects($this->once())
                        ->method('update')
                        ->willReturn($expect_result)
        ;
        $result = $this->cardService->handleChangeColumn(1, 2);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleUpdateDescription(): void
    {
        $expect_result = self::$MATCHED_CARD;
        $expect_result['description'] = 'update description';
        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkExistedCard']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->once())
                          ->method('checkExistedCard')
                          ->willReturn(self::$MATCHED_CARD)
        ;
        $this->cardModel->expects($this->once())
                        ->method('update')
                        ->willReturn($expect_result)
        ;
        $result = $this->cardService->handleUpdateDescription(1, 'update description');
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleGetCardsByColumn(): void
    {
        $this->cardModel->expects($this->once())
                        ->method('join')
                        ->willReturn([
                            [
                                "id"                     => 1,
                                "column_id"              => 65,
                                "title"                  => "card 2",
                                "description"            => "<p>Hello world<\/p><p><br><\/p>",
                                "created_at"             => "2023-12-11 15:42:22.095",
                                "updated_at"             => "2023-12-11 15:42:22.095",
                                "status"                 => "1",
                                "assigned_user"          => 1,
                                "assigned_user_id"       => 1,
                                "assigned_user_username" => "haotruong",
                                "assigned_user_alias"    => "Truong Van Hao",
                                "assigned_user_email"    => "truongvanhao159@gmail.com",
                                "position"               => 1
                            ]
                        ])
        ;

        $this->converter->expects($this->once())
                        ->method('toCardResponse')
                        ->willReturn(
                            [
                                "id"            => 1,
                                "column_id"     => 65,
                                "title"         => "card 2",
                                "description"   => "<p>Hello world<\/p><p><br><\/p>",
                                "created_at"    => "2023-12-11 15:42:22.095",
                                "updated_at"    => "2023-12-11 15:42:22.095",
                                "status"        => "1",
                                "assigned_user" => [
                                    "id"       => 25,
                                    "username" => "haotruong",
                                    "alias"    => "Truong Van Hao",
                                    "email"    => "truongvanhao159@gmail.com"
                                ],
                                "position"      => 1
                            ]
                        )
        ;

        $expect_result = [
            [
                "id"            => 1,
                "column_id"     => 65,
                "title"         => "card 2",
                "description"   => "<p>Hello world<\/p><p><br><\/p>",
                "created_at"    => "2023-12-11 15:42:22.095",
                "updated_at"    => "2023-12-11 15:42:22.095",
                "status"        => "1",
                "assigned_user" => [
                    "id"       => 25,
                    "username" => "haotruong",
                    "alias"    => "Truong Van Hao",
                    "email"    => "truongvanhao159@gmail.com"
                ],
                "position"      => 1
            ]
        ];
        $result = $this->cardService->handleGetCardsByColumn(65);
        $this->assertEquals($expect_result, $result);
    }

    public function testHandleMoveCardInBoardWithTheSameColumn(): void
    {
        $original_card = self::$MATCHED_CARD;
        $original_card['id'] = 1;
        $original_card['position'] = 1;
        $target_card = self::$MATCHED_CARD;
        $target_card['id'] = 2;
        $target_card['position'] = 2;

        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkCardInColumn']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->exactly(2))
                          ->method('checkCardInColumn')
                          ->willReturn($original_card, $target_card)
        ;
        $this->cardModel->expects($this->exactly(2))
                        ->method('update')
        ;
        $this->cardService->handleMoveCardInBoard(1, 1, 2, 1);
    }

    public function testHandleMoveCardInBoardWithTheDifferentColumn(): void
    {
        $original_card = self::$MATCHED_CARD;
        $original_card['id'] = 1;
        $original_card['position'] = 1;
        $target_card = self::$MATCHED_CARD;
        $target_card['id'] = 2;
        $target_card['position'] = 2;

        $this->cardService = $this->createPartialMock(
            CardService::class,
            ['checkCardInColumn']
        );
        $this->cardService->__construct($this->cardModel, $this->converter);
        $this->cardService->expects($this->exactly(2))
                          ->method('checkCardInColumn')
                          ->willReturn($original_card, $target_card)
        ;
        $this->cardModel->expects($this->once())
                        ->method('find')
                        ->willReturn([
                            [
                                'assigned_user' => 1,
                                'column_id'     => 2,
                                'created_at'    => '2023-12-11 22:28:33.019',
                                'updated_at'    => '2023-12-11 22:28:33.019',
                                'description'   => 'Abc description',
                                'id'            => 3,
                                'position'      => 3,
                                'status'        => "1",
                                'title'         => 'Card 1 title',
                            ],
                            [
                                'assigned_user' => 1,
                                'column_id'     => 2,
                                'created_at'    => '2023-12-11 22:28:33.019',
                                'updated_at'    => '2023-12-11 22:28:33.019',
                                'description'   => 'Abc description',
                                'id'            => 4,
                                'position'      => 4,
                                'status'        => "1",
                                'title'         => 'Card 1 title',
                            ]
                        ])
        ;
        $this->cardModel->expects($this->exactly(3))
                        ->method('update')
        ;
        $this->cardService->handleMoveCardInBoard(1, 1, 2, 2);
    }
}
