<?php
declare(strict_types=1);

use app\entities\UserEntity;
use app\models\UserModel;
use app\services\AuthService;
use app\services\JwtService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use shared\enums\ErrorMessage;
use shared\enums\TypeJwt;
use shared\exceptions\ResponseException;

class AuthServiceTest extends TestCase
{
    private AuthService $authService;
    private MockObject $userModelMock;
    private MockObject $jwtServiceMock;
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
        $this->jwtServiceMock = $this->createMock(JwtService::class);

        $this->authService = new AuthService($this->userModelMock, $this->jwtServiceMock);
    }

    public function testHandleRegisterSuccess()
    {
        $this->userModelMock->expects($this->once())
                            ->method('findOne')
                            ->willReturn(null)
        ;

        $this->userModelMock->expects($this->once())
                            ->method('save')
                            ->willReturn(self::$MATCHED_USER)
        ;

        $user_entity = new UserEntity();
        $user_entity->setUsername('haotruong');
        $user_entity->setPassword('12345678');
        $user_entity->setEmail('truongvanhao@gmail.com');
        $user_entity->setAlias('Truong Van Hao');

        $result = $this->authService->handleRegister($user_entity);

        $this->assertEquals(self::$MATCHED_USER, $result);
    }

    public function testHandleRegisterFail()
    {
        $this->userModelMock->expects($this->once())
                            ->method('findOne')
                            ->willReturn(self::$MATCHED_USER)
        ;

        $user_entity = new UserEntity();
        $user_entity->setUsername('haotruong');
        $user_entity->setPassword('12345678');
        $user_entity->setEmail('truongvanhao@gmail.com');
        $user_entity->setAlias('Truong Van Hao');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::EXISTED_USERNAME->value);

        $this->authService->handleRegister($user_entity);
    }

    public function testHandleLoginWrongUsername()
    {
        $this->userModelMock->expects($this->once())
                            ->method('findOne')
                            ->willReturn(null)
        ;

        $user_entity = new UserEntity();
        $user_entity->setUsername('haotruong');
        $user_entity->setPassword('12345678');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::WRONG_USERNAME_OR_PASSWORD->value);

        $this->authService->handleLogin($user_entity);
    }

    public function testHandleLoginWrongPassword()
    {
        $this->userModelMock->expects($this->once())
                            ->method('findOne')
                            ->willReturn(self::$MATCHED_USER)
        ;

        $user_entity = new UserEntity();
        $user_entity->setUsername('haotruong');
        $user_entity->setPassword('123456789');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::WRONG_USERNAME_OR_PASSWORD->value);

        $this->authService->handleLogin($user_entity);
    }

    public function testHandleLoginSuccess()
    {
        $this->userModelMock->expects($this->once())
                            ->method('findOne')
                            ->willReturn(self::$MATCHED_USER)
        ;

        $this->userModelMock->expects($this->once())
                            ->method('update')
                            ->willReturn(self::$MATCHED_USER)
        ;

        $user_entity = new UserEntity();
        $user_entity->setUsername('haotruong');
        $user_entity->setPassword('12345678');

        $matcher_jwt_service = $this->exactly(2);
        $this->jwtServiceMock->expects($matcher_jwt_service)
                             ->method('generateToken')
                             ->willReturnCallback(
                                 function (TypeJwt $type, int $user_id) use ($matcher_jwt_service) {
                                     match ([
                                         $type,
                                         $user_id
                                     ]) {
                                         [
                                             TypeJwt::ACCESS_TOKEN,
                                             self::$MATCHED_USER['id']
                                         ] => 'mockedAccessToken',
                                         [
                                             TypeJwt::REFRESH_TOKEN,
                                             self::$MATCHED_USER['id']
                                         ] => 'mockedRefreshToken'
                                     };
                                 }
                             )->willReturn('mockedAccessToken', 'mockedRefreshToken')
        ;

        $result = $this->authService->handleLogin($user_entity);
        $expect_result = [
            'accessToken'  => 'mockedAccessToken',
            'refreshToken' => 'mockedRefreshToken',
        ];

        $this->assertEquals(
            $expect_result, $result
        );
    }

    public function testHandleLogoutFail()
    {
        $this->userModelMock->expects($this->once())
                            ->method('findOne')
                            ->willReturn(null)
        ;

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage(ErrorMessage::USER_NOT_FOUND->value);
        $this->authService->handleLogout(1);
    }

    public function testHandleLogoutSuccess()
    {
        $expect_result = self::$MATCHED_USER;
        $expect_result['access_token'] = null;
        $expect_result['refresh_token'] = null;

        $this->userModelMock->expects($this->once())
                            ->method('findOne')
                            ->willReturn(self::$MATCHED_USER)
        ;

        $this->userModelMock->expects($this->once())
                            ->method('update')
                            ->willReturn($expect_result)
        ;

        $result = $this->authService->handleLogout(1);
        $this->assertEquals($expect_result, $result);
    }
}
