<?php

namespace Parser\Parser;

use Parser\Model\Site;

abstract class BaseParser implements ParserInterface
{
    protected const PHONE_PATTERN = '/\+?[78][\s\-]?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}/';
    protected const PHONE_PATTERN_8800 = '/8800\d{7}/';
    protected const EMAIL_PATTERN = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
    protected const INN_PATTERN = '/\b\d{10}\b|\b\d{12}\b/';
    protected const OGRN_PATTERN = '/\b\d{13}\b/';
    protected const OGRNIP_PATTERN = '/\b\d{15}\b/';
    protected const TELEGRAM_PATTERN = '/https?:\/\/(?:t\.me|telegram\.me)\/[a-zA-Z0-9_]+/';
    protected const VK_PATTERN = '/https?:\/\/(?:vk\.com|vkontakte\.ru)\/[a-zA-Z0-9_]+/';
    protected const OK_PATTERN = '/https?:\/\/ok\.ru\/[a-zA-Z0-9_]+/';
    protected const SPB_PATTERN = '/(?:санкт-петербург|спб|питер|st\.?\s*petersburg)/i';

    /**
     * Очищает HTML контент от тегов и стилей
     * Заменяет определенные теги на переносы строк
     * 
     * @param string $content Исходный HTML контент
     * @return string Очищенный текст
     */
    protected function cleanContent(string $content): string
    {
        // Удаляем стили и скрипты
        $content = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $content);
        //$content = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $content);
        $content = preg_replace('/<script[^>]*>/is', '', $content);
        $content = preg_replace('/<\/script>/is', '', $content);
        
        // Заменяем теги на переносы строк
        $content = str_replace(['<br>', '<br/>', '<br />', '</p>', '</div>'], "\n", $content);
        
        // Удаляем все остальные HTML теги
        $content = strip_tags($content);
        
        // Удаляем множественные пробелы и переносы строк
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Удаляем пробелы в начале и конце строки
        $content = trim($content);
        echo "\n---------------------------------\n";
        echo $content;
        echo "\n---------------------------------\n";
        return $content;
    }

    protected function findPhones(string $content, Site $site): bool
    {
        $pri = false;
        $content = $this->cleanContent($content);
        
        if (preg_match_all(self::PHONE_PATTERN_8800, $content, $matches)) {
            $info = 'contact_800';
            foreach ($matches[0] as $phone) {
                // Ищем контекст вокруг номера телефона
                if (preg_match('/(?:\w+\s+){0,2}'.$phone.'(?:\s+\w+){0,2}/', $content, $context)) {
                    $contextWords = array_filter(explode(' ', $context[0]));
                    $contextWords = array_diff($contextWords, [$phone]);
                    if (!empty($contextWords)) {
                        $info .= ' ' . implode(' ', $contextWords);
                    }
                }
                $site->addPhone($phone, $info);
                $pri = true;
            }
        }   
        
        if (preg_match_all(self::PHONE_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $phone) {
                $info = 'contact';
                
                // Ищем контекст вокруг номера телефона
                if (preg_match('/(?:\w+\s+){0,2}'.$phone.'(?:\s+\w+){0,2}/', $content, $context)) {
                    $contextWords = array_filter(explode(' ', $context[0]));
                    $contextWords = array_diff($contextWords, [$phone]);
                    if (!empty($contextWords)) {
                        $info .= ' ' . implode(' ', $contextWords);
                    }
                }
                
                $site->addPhone($phone, $info);
                $pri = true;
            }
        }
        
        return $pri;
    }

    protected function findEmails(string $content, Site $site): bool
    {
        $pri = false;
        $content = $this->cleanContent($content);
        if (preg_match_all(self::EMAIL_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $email) {
                $site->addEmail($email, 'contact');
                $pri = true;
            }
        }
        return $pri;
    }

    protected function findInn(string $content, Site $site): bool
    {
        $pri = false;
        $content = $this->cleanContent($content);
        if (preg_match_all(self::INN_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $inn) {
                $site->addInn($inn, 'found');
                $pri = true;
            }
        }
        return $pri;
    }

    protected function findOgrn(string $content, Site $site): bool
    {
        $pri = false;
        $content = $this->cleanContent($content);
        if (preg_match_all(self::OGRN_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $ogrn) {
                $site->addOgrn($ogrn, 'found');
                $pri = true;
            }
        }
        return $pri;
    }

    protected function findOgrnip(string $content, Site $site): bool
    {
        $pri = false;
        $content = $this->cleanContent($content);
        if (preg_match_all(self::OGRNIP_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $ogrnip) {
                $site->addOgrnip($ogrnip, 'found');
                $pri = true;
            }
        }
        return $pri;
    }

    protected function findSocialLinks(string $content, Site $site): bool
    {
        $pri = false;
        if (preg_match_all(self::TELEGRAM_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $url) {
                $site->addTelegram($url, 'telegram');
                $pri = true;
            }
        }

        if (preg_match_all(self::VK_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $url) {
                $site->addUrl($url, 'vk');
                $pri = true;
            }
        }

        if (preg_match_all(self::OK_PATTERN, $content, $matches)) {
            foreach ($matches[0] as $url) {
                $site->addUrl($url, 'ok');
                $pri = true;
            }
        }
        return $pri;
    }

    protected function checkSpb(string $content, Site $site): bool
    {
        $pri = false;
        $content = $this->cleanContent($content);
        if (preg_match(self::SPB_PATTERN, $content)) {
            $site->setSpb(true);
            $pri = true;
        }
        return $pri;
    }
} 