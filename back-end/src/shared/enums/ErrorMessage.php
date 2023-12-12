<?php
declare(strict_types=1);

namespace shared\enums;

enum ErrorMessage: string
{
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
    case COLUMN_NOT_FOUND = 'Column not found';
    case COLUMN_NOT_IN_BOARD = 'Column is not in this board!';
    case COUPLE_COLUMN_NOT_IN_THE_SAME_BOARD = 'This couple column is not in the same board';
    case STATUS_CARD_NOT_ALLOW = 'Status card is not allowed in range[1,4]';
    case CARD_NOT_IN_COLUMN = 'Card is not in this column!';
    case CARD_NOT_FOUND = 'Card not found!';
    case USER_WAS_ASSIGNED_USER_OF_THIS_CARD = 'This user was the assigned user of this card!';
}
