<?php

namespace App\ArchEngine\Enums;

enum MutationType: string
{
    case CreateFile = 'create_file';
    case EditFile = 'edit_file';
    case RunCommand = 'run_command';
}
