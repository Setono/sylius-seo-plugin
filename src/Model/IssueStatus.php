<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Model;

enum IssueStatus: string
{
    /** Detected and currently present */
    case Open = 'open';

    /** Deliberately dismissed by an operator; never auto-resurrected */
    case Ignored = 'ignored';

    /** Previously open, but not detected on the most recent run */
    case Resolved = 'resolved';
}
