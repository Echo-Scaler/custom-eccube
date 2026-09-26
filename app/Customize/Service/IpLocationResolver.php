<?php

/*
 * IP Location & Region Resolver
 * Resolves geolocation region, city, country, and flag emoji from IP addresses with local disk caching.
 */

namespace Customize\Service;

class IpLocationResolver
{
    private string $cacheFile;
    private static array $memoryCache = [];
    private array $diskCache = [];
    private bool $cacheDirty = false;

    public function __construct(string $projectDir)
    {
        $cacheDir = $projectDir . '/var/cache';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0775, true);
        }
        $this->cacheFile = $cacheDir . '/ip_location_cache.json';
        $this->loadDiskCache();
    }

    public function __destruct()
    {
        $this->saveDiskCache();
    }

    /**
     * Resolve full geolocation details for an IP.
     *
     * @return array{ip: string, is_local: bool, country: string, country_code: string, region: string, city: string, flag: string, formatted: string}
     */
    public function resolve(?string $ip): array
    {
        $ip = trim((string) $ip);
        if ($ip === '') {
            return $this->buildResult('', false, 'Unknown', '', '', '', '🌐', '🌐 Unknown');
        }

        // Check memory cache
        if (isset(self::$memoryCache[$ip])) {
            return self::$memoryCache[$ip];
        }

        // Check disk cache
        if (isset($this->diskCache[$ip])) {
            self::$memoryCache[$ip] = $this->diskCache[$ip];
            return $this->diskCache[$ip];
        }

        // Check for private / loopback / LAN range
        if (!$this->isPublicIp($ip)) {
            $result = $this->buildResult(
                $ip,
                true,
                'Local Network',
                '',
                'LAN',
                'Localhost',
                '🏠',
                '🏠 Local / LAN'
            );
            $this->cacheResult($ip, $result);
            return $result;
        }

        // Query external geolocation API with short timeout
        $geo = $this->fetchFromIpApi($ip);
        if (!$geo) {
            $geo = $this->fetchFromIp2c($ip);
        }

        if ($geo) {
            $code = strtoupper(trim($geo['country_code'] ?? ''));
            $flag = $this->countryCodeToFlag($code);
            $country = trim($geo['country'] ?? '');
            $region = trim($geo['region'] ?? '');
            $city = trim($geo['city'] ?? '');

            $parts = array_unique(array_filter([$region, $country]));
            $formatted = $flag . ' ' . implode(', ', $parts);

            $result = $this->buildResult(
                $ip,
                false,
                $country,
                $code,
                $region,
                $city,
                $flag,
                $formatted
            );
        } else {
            $result = $this->buildResult(
                $ip,
                false,
                'Unknown',
                '',
                '',
                '',
                '🌐',
                '🌐 Unknown'
            );
        }

        $this->cacheResult($ip, $result);
        return $result;
    }

    /**
     * Get human-readable formatted region string (e.g., "🇯🇵 Tokyo, Japan").
     */
    public function formatRegion(?string $ip): string
    {
        $data = $this->resolve($ip);
        return $data['formatted'];
    }

    /**
     * Get two-letter country code (e.g., "JP", "US").
     */
    public function getCountryCode(?string $ip): string
    {
        $data = $this->resolve($ip);
        return $data['country_code'];
    }

    /**
     * Convert ISO 3166-1 alpha-2 country code to emoji flag.
     */
    public function countryCodeToFlag(string $code): string
    {
        $code = strtoupper(trim($code));
        if (strlen($code) !== 2 || !ctype_alpha($code)) {
            return '🌐';
        }

        $f1 = ord($code[0]) - 65 + 0x1F1E6;
        $f2 = ord($code[1]) - 65 + 0x1F1E6;

        return mb_chr($f1, 'UTF-8') . mb_chr($f2, 'UTF-8');
    }

    /**
     * Check if an IP address is a routable public Internet address.
     */
    private function isPublicIp(string $ip): bool
    {
        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * Query ip-api.com service.
     */
    private function fetchFromIpApi(string $ip): ?array
    {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 1.5,
                'header' => "User-Agent: EC-CUBE-UserHistory/1.0\r\n",
            ],
        ]);

        $url = 'http://ip-api.com/json/' . rawurlencode($ip) . '?fields=status,country,countryCode,regionName,city';
        $json = @file_get_contents($url, false, $ctx);
        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);
        if (!is_array($data) || ($data['status'] ?? '') !== 'success') {
            return null;
        }

        return [
            'country' => $data['country'] ?? '',
            'country_code' => $data['countryCode'] ?? '',
            'region' => $data['regionName'] ?? '',
            'city' => $data['city'] ?? '',
        ];
    }

    /**
     * Fallback to ip2c.org service.
     */
    private function fetchFromIp2c(string $ip): ?array
    {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 1.5,
                'header' => "User-Agent: EC-CUBE-UserHistory/1.0\r\n",
            ],
        ]);

        $response = @file_get_contents('https://ip2c.org/' . rawurlencode($ip), false, $ctx);
        if (!$response) {
            return null;
        }

        // ip2c format: 1;ISO2;ISO3;Country Name
        $parts = explode(';', trim($response));
        if (count($parts) >= 4 && $parts[0] === '1') {
            return [
                'country' => $parts[3],
                'country_code' => $parts[1],
                'region' => '',
                'city' => '',
            ];
        }

        return null;
    }

    private function buildResult(
        string $ip,
        bool $isLocal,
        string $country,
        string $countryCode,
        string $region,
        string $city,
        string $flag,
        string $formatted
    ): array {
        return [
            'ip' => $ip,
            'is_local' => $isLocal,
            'country' => $country,
            'country_code' => $countryCode,
            'region' => $region,
            'city' => $city,
            'flag' => $flag,
            'formatted' => $formatted,
        ];
    }

    private function cacheResult(string $ip, array $result): void
    {
        self::$memoryCache[$ip] = $result;
        $this->diskCache[$ip] = $result;
        $this->cacheDirty = true;
    }

    private function loadDiskCache(): void
    {
        if (file_exists($this->cacheFile)) {
            $content = @file_get_contents($this->cacheFile);
            if ($content) {
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    $this->diskCache = $decoded;
                }
            }
        }
    }

    private function saveDiskCache(): void
    {
        if (!$this->cacheDirty) {
            return;
        }

        // Keep maximum 5000 cached IPs to prevent excessive file size
        if (count($this->diskCache) > 5000) {
            $this->diskCache = array_slice($this->diskCache, -5000, 5000, true);
        }

        @file_put_contents(
            $this->cacheFile,
            json_encode($this->diskCache, JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
        $this->cacheDirty = false;
    }
}
