<?php

declare(strict_types=1);

namespace shared\enums;

enum SuccessMessage: string
{
    case REGISTER_SUCCESSFULLY = 'Register successfully!';
    case LOGOUT_SUCCESSFULLY = 'Logout successfully!';
    case DELETE_SUCCESSFULLY = 'Delete successfully!';
    case SWAP_POSITION_SUCCESSFULLY = 'Swap position of two column successfully!';
    case ASSIGN_USER_TO_CARD_SUCCESSFULLY = 'Assign this user to card successfully!';
    case CHANGE_COLUMN_SUCCESSFULLY = 'Change column for this card successfully!';
    case SWAP_TWO_CARDS_SUCCESSFULLY = 'Move card successfully!';
}
