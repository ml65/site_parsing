<?php

namespace Parser\Model;

class Site
{
    private string $domain;
    private int $id;
    private string $redirectDomain;
    private string $redurectUrl;
    private array $visitedUrls = [];
    private array $phones = [];
    private array $emails = [];
    private array $urls = [];
    private array $telegram = [];
    private array $inn = [];
    private array $ogrn = [];
    private array $ogrnip = [];
    private array $spb = [];

    public function __construct(string $domain, int $id=0)
    {
        $this->domain = $domain;
        $this->id = $id;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setRedirectDomain(?string $redirectDomain): void
    {
        $this->redirectDomain = $redirectDomain;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getRedirectDomain(): ?string
    {
        $redirectDomain = $this->redirectDomain ?? null;
        return $redirectDomain;
    }

    public function setredirectUrl(?string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    public function getredirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function addVisitedUrls(string $url): void
    {
        $this->visitedUrls[$url] = true;
    }

    public function getVisitedUrls(): array
    {
        return $this->visitedUrls;
    }

    public function isVisitedUrls(string $url): bool
    {
        return isset($this->visitedUrls[$url]);
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

    public function addUrls(string $url, string $description): void
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

    public function addSpb(string $spb, string $description): void
    {
        var_dump($this->spb);
        echo "=1=",$spb,"\n=2=",$description,"\n";
        $this->spb[$spb] = $description;
        var_dump($this->spb);
    }

    public function getSpb(): array
    {
        return $this->spb;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'domain' => $this->domain,
            'redirect_domain' => $this->redirectDomain??'',
            'redirect_url' => $this->redurectUrl??'',
            'visited_urls' => $this->visitedUrls??'',
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