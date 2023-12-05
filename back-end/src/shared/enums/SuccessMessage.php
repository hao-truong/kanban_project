<?php
declare(strict_types=1);

namespace shared\enums;

enum SuccessMessage: string {
    case REGISTER_SUCCESSFULLY = 'Register successfully!';
    case LOGOUT_SUCCESSFULLY = 'Logout successfully!';
    case DELETE_SUCCESSFULLY = 'Delete successfully!';
}
