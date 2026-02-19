<?php

declare(strict_types=1);

namespace Src\Project\Domain;

interface TaskRepository extends TaskReadRepository, TaskWriteRepository {}
