<?php

namespace Parser\Parser;

use Parser\Model\Site;

interface ParserInterface
{
    public function parse(string $content, Site $site): bool;
} 