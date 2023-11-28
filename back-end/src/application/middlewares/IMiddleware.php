<?php
declare(strict_types=1);

namespace app\middlewares;

interface IMiddleware {
    public function execute();
}
