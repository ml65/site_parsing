<?php

namespace Parser\Parser;

use Parser\Model\Site;

abstract class BaseParser implements ParserInterface
{
    protected const PHONE_PATTERN = '/\+?[78][\s\-]?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}/';
    protected const EMAIL_PATTERN = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
    protected const INN_PATTERN = '/\b\d{10}\b|\b\d{12}\b/';
    protected const OGRN_PATTERN = '/\b\d{13}\b/';
    protected const OGRNIP_PATTERN = '/\b\d{15}\b/';
    protected const TELEGRAM_PATTERN = '/https?:\/\/(?:t\.me|telegram\.me)\/[a-zA-Z0-9_]+/';
    protected const VK_PATTERN = '/https?:\/\/(?:vk\.com|vkontakte\.ru)\/[a-zA-Z0-9_]+/';
    protected const OK_PATTERN = '/https?:\/\/ok\.ru\/[a-zA-Z0-9_]+/';
    protected const SPB_PATTERN = '/(?:санкт-петербург|спб|питер|st\.?\s*petersburg)/i';

    protected function findPhones(string $content, Site $site): void
    {
        if (preg_match_all(self::PHONE_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $phone) {
                $site->addPhone($phone, 'contact');
            }
        }
    }

    protected function findEmails(string $content, Site $site): void
    {
        if (preg_match_all(self::EMAIL_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $email) {
                $site->addEmail($email, 'contact');
            }
        }
    }

    protected function findInn(string $content, Site $site): void
    {
        if (preg_match_all(self::INN_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $inn) {
                $site->addInn($inn, 'found');
            }
        }
    }

    protected function findOgrn(string $content, Site $site): void
    {
        if (preg_match_all(self::OGRN_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $ogrn) {
                $site->addOgrn($ogrn, 'found');
            }
        }
    }

    protected function findOgrnip(string $content, Site $site): void
    {
        if (preg_match_all(self::OGRNIP_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $ogrnip) {
                $site->addOgrnip($ogrnip, 'found');
            }
        }
    }

    protected function findSocialLinks(string $content, Site $site): void
    {
        if (preg_match_all(self::TELEGRAM_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $url) {
                $site->addTelegram($url, 'telegram');
            }
        }

        if (preg_match_all(self::VK_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $url) {
                $site->addUrl($url, 'vk');
            }
        }

        if (preg_match_all(self::OK_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $url) {
                $site->addUrl($url, 'ok');
            }
        }
    }

    protected function checkSpb(string $content, Site $site): void
    {
        if (preg_match(self::SPB_PATTERN, $content)) {
            $site->setSpb(true);
        }
    }
} 