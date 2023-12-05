<?php
declare(strict_types=1);

namespace shared\enums;

enum ResponseMessage: string {
    case REGISTER_SUCCESSFULLY = 'Register successfully!';
    case LOGOUT_SUCCESSFULLY = 'Logout successfully!';
}
