<?php
declare(strict_types=1);

/** Escapa texto para HTML. */
function e(mixed $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
