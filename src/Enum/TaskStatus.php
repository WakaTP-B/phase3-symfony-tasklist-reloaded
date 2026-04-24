<?php
namespace App\Enum;

enum TaskStatus: string
{
    case PENDING = 'En cours';
    case COMPLETED = 'Terminée';
    case ARCHIVED = 'Archivée';
}