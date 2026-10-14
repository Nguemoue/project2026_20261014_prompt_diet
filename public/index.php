<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

echo PromptDiet\Web::handle($_GET, $_POST);
