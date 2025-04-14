<?php

namespace Parser\Reader;

class CsvReader
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function read(): array
    {
        $sites = [];
        
        if (($handle = fopen($this->filename, "r")) !== false) {
            // Пропускаем заголовок
            fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) >= 2) {
                    $sites[$data[1]] = [
                        'id' => $data[0],
                        'domain' => $data[1]
                    ];
                }
            }
            
            fclose($handle);
        }
        
        return $sites;
    }
} 