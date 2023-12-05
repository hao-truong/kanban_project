<?php
declare(strict_types=1);

namespace shared\enums;

enum ErrorMessage: string {
    case EXISTED_USERNAME = 'Username existed!';
    case WRONG_USERNAME_OR_PASSWORD = "Username or password is wrong!";
    case USER_NOT_FOUND = 'User not found';
    case BOARD_NOT_FOUND = 'Board not found';
    case NOT_BOARD_OWNER = "You are not the owner of the board!";
    case NOT_BOARD_MEMBER = 'you are not the member of the board';
    case JOINED_MEMBER_BOARD = 'This user was a member of the board';
    case UNAUTHORIZED_TOKEN = 'Unauthorized token';
    case EXPIRED_TOKEN = 'Token is expired';
    case INTERNAL_SERVER_ERROR = 'Internal server error';
}
