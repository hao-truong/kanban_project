<?php


namespace services;

use app\models\UserModel;
use app\services\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use shared\enums\ErrorMessage;
use shared\exceptions\ResponseException;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private MockObject $userModelMock;
    private static array $MATCHED_USER = [
        "id"            => 1,
        "username"      => "haotruong",
        "password"      => "\$2y\$10\$Gpbhf3nSohUzO3dfiMuRfO4l9Xer0y5BS4rXxSQz9kLaDgaR1HIO2",
        "email"         => "truongvanhao@gmail.com",
        "alias"         => "Truong Van Hao",
        "access_token"  => "mockedAccessToken",
        "refresh_token" => "mockedRefreshToken"
    ];

    protected function setUp(): void
    {
        $this->userModelMock = $this->createMock(UserModel::class);

        $this->userService = new UserService($this->userModelMock);
    }

    public function testHandleGetProfileFail(): void
    {
        $this->userModelMock->expects($this->once())
                            ->method('findOne')->willReturn(null)
        ;

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::USER_NOT_FOUND->value);

        $this->userService->handleGetProfile(1);
    }

    public function testHandleGetProfileSuccess(): void
    {
        $expect_result = [
            'id'       => self::$MATCHED_USER['id'],
            'username' => self::$MATCHED_USER['username'],
            'email'    => self::$MATCHED_USER['email'],
            'alias'    => self::$MATCHED_USER['alias'],
        ];
        $this->userModelMock->expects($this->once())
                            ->method('findOne')->willReturn(self::$MATCHED_USER)
        ;

        $result = $this->userService->handleGetProfile(1);
        $this->assertEquals($expect_result, $result);
    }

    public function testGetUserByUsernameFail(): void
    {
        $this->userModelMock->expects($this->once())
                            ->method('findOne')->willReturn(null)
        ;

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::USER_NOT_FOUND->value);

        $this->userService->getUserByUsername(1);
    }

    public function testGetUserByUsernameSuccess(): void
    {
        $expect_result = self::$MATCHED_USER;
        $this->userModelMock->expects($this->once())
                            ->method('findOne')->willReturn(self::$MATCHED_USER)
        ;

        $result = $this->userService->getUserByUsername(1);
        $this->assertEquals($expect_result, $result);
    }
}
