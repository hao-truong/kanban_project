<?php

declare(strict_types=1);

namespace shared\enums;

enum TypeJwt
{
    case ACCESS_TOKEN;
    case REFRESH_TOKEN;
}
