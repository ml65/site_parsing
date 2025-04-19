<?php

namespace Parser\Parser;

use Parser\Model\Site;

class HtmlParser extends BaseParser
{
    public function parse(string $content, Site $site, string $comment): bool
    {
        $priPhones = $this->findPhones($content, $site, $comment);
        $priEmails = $this->findEmails($content, $site, $comment);
        $priInn    = $this->findInn($content, $site, $comment);
        $priOgrn   = $this->findOgrn($content, $site, $comment);
        $priOgrnip = $this->findOgrnip($content, $site, $comment);
        $priSL     = $this->findSocialLinks($content, $site, $comment);
        $priSpb    = $this->checkSpb($content, $site, $comment);

        return $priPhones || $priEmails || $priInn || $priOgrn || $priOgrnip || $priSL || $priSpb;
    
    }
} 