<?php

namespace Parser\Parser;

use Parser\Model\Site;

class HtmlParser extends BaseParser
{
    public function parse(string $content, Site $site): bool
    {
        return 
            $this->findPhones($content, $site) ||
            $this->findEmails($content, $site) ||
            $this->findInn($content, $site) ||
            $this->findOgrn($content, $site) ||
            $this->findOgrnip($content, $site) ||
            $this->findSocialLinks($content, $site) ||
            $this->checkSpb($content, $site);
    
    }
} 