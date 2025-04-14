<?php

namespace Parser\Model;

class Site
{
    private string $domain;
    private ?string $redirectDomain = null;
    private ?string $parentDomain = null;
    private array $phones = [];
    private array $emails = [];
    private array $urls = [];
    private array $telegram = [];
    private array $inn = [];
    private array $ogrn = [];
    private array $ogrnip = [];
    private bool $spb = false;

    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setRedirectDomain(?string $redirectDomain): void
    {
        $this->redirectDomain = $redirectDomain;
    }

    public function getRedirectDomain(): ?string
    {
        return $this->redirectDomain;
    }

    public function setParentDomain(?string $parentDomain): void
    {
        $this->parentDomain = $parentDomain;
    }

    public function getParentDomain(): ?string
    {
        return $this->parentDomain;
    }

    public function addPhone(string $phone, string $description): void
    {
        $this->phones[$phone] = $description;
    }

    public function getPhones(): array
    {
        return $this->phones;
    }

    public function addEmail(string $email, string $description): void
    {
        $this->emails[$email] = $description;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function addUrl(string $url, string $description): void
    {
        $this->urls[$url] = $description;
    }

    public function getUrls(): array
    {
        return $this->urls;
    }

    public function addTelegram(string $url, string $description): void
    {
        $this->telegram[$url] = $description;
    }

    public function getTelegram(): array
    {
        return $this->telegram;
    }

    public function addInn(string $inn, string $description): void
    {
        $this->inn[$inn] = $description;
    }

    public function getInn(): array
    {
        return $this->inn;
    }

    public function addOgrn(string $ogrn, string $description): void
    {
        $this->ogrn[$ogrn] = $description;
    }

    public function getOgrn(): array
    {
        return $this->ogrn;
    }

    public function addOgrnip(string $ogrnip, string $description): void
    {
        $this->ogrnip[$ogrnip] = $description;
    }

    public function getOgrnip(): array
    {
        return $this->ogrnip;
    }

    public function setSpb(bool $spb): void
    {
        $this->spb = $spb;
    }

    public function isSpb(): bool
    {
        return $this->spb;
    }

    public function toArray(): array
    {
        return [
            'domain' => $this->domain,
            'redirect_domain' => $this->redirectDomain,
            'parent_domain' => $this->parentDomain,
            'phones' => $this->phones,
            'emails' => $this->emails,
            'urls' => $this->urls,
            'telegram' => $this->telegram,
            'inn' => $this->inn,
            'ogrn' => $this->ogrn,
            'ogrnip' => $this->ogrnip,
            'spb' => $this->spb
        ];
    }
} 