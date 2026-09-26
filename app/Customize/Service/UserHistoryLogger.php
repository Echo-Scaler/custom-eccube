<?php

/*
 * User History Logger Service
 * Handles recording, parsing, searching, filtering, and exporting user activity log files.
 */

namespace Customize\Service;

class UserHistoryLogger
{
    private string $logDir;

    public function __construct(string $projectDir)
    {
        $this->logDir = rtrim($projectDir, '/') . '/var/log/user_history';
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0775, true);
        }
    }

    /**
     * Log a user history record to the daily log file.
     *
     * @param string $eventType e.g. PAGE_VIEW, USER_LOGIN, USER_LOGOUT, PRODUCT_VIEW, PURCHASE_COMPLETE, etc.
     * @param array $data Contextual information about the user and request.
     */
    public function log(string $eventType, array $data = []): void
    {
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0775, true);
        }

        $now = new \DateTimeImmutable('now', new \DateTimeZone(date_default_timezone_get() ?: 'Asia/Tokyo'));
        $today = $now->format('Y-m-d');
        $filePath = $this->logDir . '/user_history_' . $today . '.log';

        $record = [
            'id' => uniqid('uh_', true),
            'timestamp' => $now->format('Y-m-d H:i:s'),
            'event' => strtoupper(trim($eventType)),
            'user_type' => $data['user_type'] ?? 'guest', // customer, guest, admin
            'user_id' => $data['user_id'] ?? null,
            'user_name' => $data['user_name'] ?? 'Guest',
            'user_email' => $data['user_email'] ?? null,
            'ip' => $data['ip'] ?? '127.0.0.1',
            'method' => strtoupper($data['method'] ?? 'GET'),
            'route' => $data['route'] ?? '',
            'url' => $data['url'] ?? '',
            'status_code' => (int) ($data['status_code'] ?? 200),
            'referer' => $data['referer'] ?? '',
            'user_agent' => $data['user_agent'] ?? '',
            'details' => $data['details'] ?? [],
        ];

        $jsonLine = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        @file_put_contents($filePath, $jsonLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * Get list of all available user history log files, newest first.
     *
     * @return array<int, array{filename: string, date: string, size_formatted: string, size_bytes: int, lines: int, path: string}>
     */
    public function getLogFiles(): array
    {
        if (!is_dir($this->logDir)) {
            return [];
        }

        $files = glob($this->logDir . '/user_history_*.log');
        if (!$files) {
            return [];
        }

        $result = [];
        foreach ($files as $filePath) {
            $filename = basename($filePath);
            $size = @filesize($filePath) ?: 0;

            // Extract date from user_history_YYYY-MM-DD.log
            preg_match('/user_history_(\d{4}-\d{2}-\d{2})\.log/', $filename, $matches);
            $date = $matches[1] ?? date('Y-m-d', filemtime($filePath));

            $result[] = [
                'filename' => $filename,
                'date' => $date,
                'size_formatted' => $this->formatFileSize($size),
                'size_bytes' => $size,
                'path' => $filePath,
            ];
        }

        // Sort descending by date/filename
        usort($result, function ($a, $b) {
            return strcmp($b['filename'], $a['filename']);
        });

        return $result;
    }

    /**
     * Determine default log file (today's file or newest existing).
     */
    public function getDefaultLogFileName(): string
    {
        $todayFile = 'user_history_' . date('Y-m-d') . '.log';
        if (file_exists($this->logDir . '/' . $todayFile)) {
            return $todayFile;
        }

        $files = $this->getLogFiles();
        if (!empty($files)) {
            return $files[0]['filename'];
        }

        return $todayFile;
    }

    /**
     * Validate and retrieve safe absolute file path for a log filename.
     */
    public function getLogFilePath(string $filename): ?string
    {
        $cleanName = basename($filename);
        if (!preg_match('/^user_history_[a-zA-Z0-9_\-]+\.log$/', $cleanName)) {
            return null;
        }

        $fullPath = $this->logDir . '/' . $cleanName;
        if (!file_exists($fullPath)) {
            return null;
        }

        return $fullPath;
    }

    /**
     * Read, filter, and paginate log records from the specified file.
     *
     * @param string $filename
     * @param array $filters ['event' => string, 'user_type' => string, 'keyword' => string, 'status_code' => string|int]
     * @param int $page
     * @param int $perPage
     * @return array{items: array, total: int, page: int, per_page: int, total_pages: int}
     */
    public function getLogs(string $filename, array $filters = [], int $page = 1, int $perPage = 25): array
    {
        $filePath = $this->getLogFilePath($filename);
        if (!$filePath) {
            return [
                'items' => [],
                'total' => 0,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => 1,
            ];
        }

        $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false || empty($lines)) {
            return [
                'items' => [],
                'total' => 0,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => 1,
            ];
        }

        // Reverse to show newest records first
        $lines = array_reverse($lines);

        $filterEvent = !empty($filters['event']) ? strtoupper(trim($filters['event'])) : null;
        $filterUserType = !empty($filters['user_type']) ? strtolower(trim($filters['user_type'])) : null;
        $filterKeyword = !empty($filters['keyword']) ? mb_strtolower(trim($filters['keyword'])) : null;
        $filterStatus = !empty($filters['status_code']) ? (int) $filters['status_code'] : null;

        $matched = [];
        foreach ($lines as $line) {
            $record = json_decode($line, true);
            if (!is_array($record)) {
                continue;
            }

            // Event filter
            if ($filterEvent !== null && ($record['event'] ?? '') !== $filterEvent) {
                continue;
            }

            // User type filter
            if ($filterUserType !== null && strtolower($record['user_type'] ?? '') !== $filterUserType) {
                continue;
            }

            // Status code filter
            if ($filterStatus !== null && (int) ($record['status_code'] ?? 0) !== $filterStatus) {
                continue;
            }

            // Keyword filter (searches across name, email, IP, URL, route, and details)
            if ($filterKeyword !== null) {
                $searchContent = mb_strtolower(
                    ($record['user_name'] ?? '') . ' ' .
                    ($record['user_email'] ?? '') . ' ' .
                    ($record['ip'] ?? '') . ' ' .
                    ($record['url'] ?? '') . ' ' .
                    ($record['route'] ?? '') . ' ' .
                    json_encode($record['details'] ?? [])
                );

                if (mb_strpos($searchContent, $filterKeyword) === false) {
                    continue;
                }
            }

            $matched[] = $record;
        }

        $total = count($matched);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;
        $items = array_slice($matched, $offset, $perPage);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
        ];
    }

    /**
     * Compute summary statistics for a log file.
     */
    public function getStats(string $filename): array
    {
        $filePath = $this->getLogFilePath($filename);
        if (!$filePath) {
            return [
                'total_events' => 0,
                'unique_users' => 0,
                'unique_ips' => 0,
                'event_counts' => [],
                'user_type_counts' => [],
            ];
        }

        $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false || empty($lines)) {
            return [
                'total_events' => 0,
                'unique_users' => 0,
                'unique_ips' => 0,
                'event_counts' => [],
                'user_type_counts' => [],
            ];
        }

        $users = [];
        $ips = [];
        $eventCounts = [];
        $userTypeCounts = [];

        foreach ($lines as $line) {
            $record = json_decode($line, true);
            if (!is_array($record)) {
                continue;
            }

            // Unique users
            if (!empty($record['user_id'])) {
                $users[$record['user_type'] . '_' . $record['user_id']] = true;
            } elseif (!empty($record['user_email'])) {
                $users[$record['user_email']] = true;
            }

            // Unique IPs
            if (!empty($record['ip'])) {
                $ips[$record['ip']] = true;
            }

            // Event counts
            $evt = $record['event'] ?? 'UNKNOWN';
            $eventCounts[$evt] = ($eventCounts[$evt] ?? 0) + 1;

            // User type counts
            $utype = $record['user_type'] ?? 'guest';
            $userTypeCounts[$utype] = ($userTypeCounts[$utype] ?? 0) + 1;
        }

        arsort($eventCounts);

        return [
            'total_events' => count($lines),
            'unique_users' => count($users),
            'unique_ips' => count($ips),
            'event_counts' => $eventCounts,
            'user_type_counts' => $userTypeCounts,
        ];
    }

    /**
     * Clear / empty a log file safely.
     */
    public function clearLog(string $filename): bool
    {
        $filePath = $this->getLogFilePath($filename);
        if (!$filePath) {
            return false;
        }

        return @file_put_contents($filePath, '') !== false;
    }

    /**
     * Export log entries as CSV string with UTF-8 BOM.
     */
    public function exportCsv(string $filename, array $filters = []): string
    {
        $logs = $this->getLogs($filename, $filters, 1, 100000);
        $fp = fopen('php://memory', 'r+');

        // Output UTF-8 BOM for Microsoft Excel compatibility
        fputs($fp, "\xEF\xBB\xBF");

        // Headers
        fputcsv($fp, [
            'ID',
            'Timestamp',
            'Event',
            'User Type',
            'User ID',
            'User Name',
            'User Email',
            'IP Address',
            'HTTP Method',
            'Route',
            'URL',
            'Status Code',
            'Details (JSON)',
        ]);

        foreach ($logs['items'] as $item) {
            fputcsv($fp, [
                $item['id'] ?? '',
                $item['timestamp'] ?? '',
                $item['event'] ?? '',
                $item['user_type'] ?? '',
                $item['user_id'] ?? '',
                $item['user_name'] ?? '',
                $item['user_email'] ?? '',
                $item['ip'] ?? '',
                $item['method'] ?? '',
                $item['route'] ?? '',
                $item['url'] ?? '',
                $item['status_code'] ?? '',
                !empty($item['details']) ? json_encode($item['details'], JSON_UNESCAPED_UNICODE) : '',
            ]);
        }

        rewind($fp);
        $content = stream_get_contents($fp);
        fclose($fp);

        return $content ?: '';
    }

    /**
     * Generate realistic sample user history records for immediate testing/demonstration.
     */
    public function generateSampleData(): void
    {
        $samples = [
            [
                'event' => 'PAGE_VIEW',
                'data' => [
                    'user_type' => 'guest',
                    'user_id' => null,
                    'user_name' => 'Guest',
                    'user_email' => null,
                    'ip' => '203.0.113.19',
                    'method' => 'GET',
                    'route' => 'homepage',
                    'url' => '/',
                    'status_code' => 200,
                    'referer' => 'https://www.google.com/',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    'details' => ['view' => 'landing_page'],
                ],
            ],
            [
                'event' => 'PRODUCT_VIEW',
                'data' => [
                    'user_type' => 'guest',
                    'user_id' => null,
                    'user_name' => 'Guest',
                    'user_email' => null,
                    'ip' => '203.0.113.19',
                    'method' => 'GET',
                    'route' => 'product_detail',
                    'url' => '/products/detail/2',
                    'status_code' => 200,
                    'referer' => 'http://localhost:8000/',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                    'details' => [
                        'product_id' => 2,
                        'product_name' => 'Percolator Mug',
                        'product_code' => 'p-002',
                        'price' => 2400,
                    ],
                ],
            ],
            [
                'event' => 'USER_LOGIN',
                'data' => [
                    'user_type' => 'customer',
                    'user_id' => 1,
                    'user_name' => 'Taro Yamada',
                    'user_email' => 'yamada@example.com',
                    'ip' => '203.0.113.19',
                    'method' => 'POST',
                    'route' => 'mypage_login',
                    'url' => '/mypage/login',
                    'status_code' => 302,
                    'referer' => 'http://localhost:8000/mypage/login',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                    'details' => ['login_method' => 'form_login', 'auth_status' => 'success'],
                ],
            ],
            [
                'event' => 'CART_ACTION',
                'data' => [
                    'user_type' => 'customer',
                    'user_id' => 1,
                    'user_name' => 'Taro Yamada',
                    'user_email' => 'yamada@example.com',
                    'ip' => '203.0.113.19',
                    'method' => 'POST',
                    'route' => 'product_add_cart',
                    'url' => '/cart/add',
                    'status_code' => 302,
                    'referer' => 'http://localhost:8000/products/detail/2',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                    'details' => [
                        'action' => 'add_item',
                        'product_id' => 2,
                        'product_name' => 'Percolator Mug',
                        'quantity' => 1,
                    ],
                ],
            ],
            [
                'event' => 'PURCHASE_COMPLETE',
                'data' => [
                    'user_type' => 'customer',
                    'user_id' => 1,
                    'user_name' => 'Taro Yamada',
                    'user_email' => 'yamada@example.com',
                    'ip' => '203.0.113.19',
                    'method' => 'POST',
                    'route' => 'shopping_complete',
                    'url' => '/shopping/complete',
                    'status_code' => 200,
                    'referer' => 'http://localhost:8000/shopping/confirm',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                    'details' => [
                        'order_id' => 101,
                        'order_no' => 'ORD-20260926-001',
                        'total' => 2400,
                        'payment_method' => 'Credit Card',
                    ],
                ],
            ],
            [
                'event' => 'USER_LOGOUT',
                'data' => [
                    'user_type' => 'customer',
                    'user_id' => 1,
                    'user_name' => 'Taro Yamada',
                    'user_email' => 'yamada@example.com',
                    'ip' => '203.0.113.19',
                    'method' => 'GET',
                    'route' => 'mypage_logout',
                    'url' => '/mypage/logout',
                    'status_code' => 302,
                    'referer' => 'http://localhost:8000/mypage',
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                    'details' => ['session_terminated' => true],
                ],
            ],
        ];

        foreach ($samples as $sample) {
            $this->log($sample['event'], $sample['data']);
        }
    }

    /**
     * Retrieve, filter, and paginate history log entries for a specific individual user.
     * Can aggregate across all available log files or a specific file.
     *
     * @param string $userType 'customer' | 'admin' | 'guest'
     * @param string $identifier User ID, email, or IP
     * @param string|null $filename Specific log file, or null/'all' to scan all available log files
     * @param array $filters ['event' => string, 'keyword' => string]
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getUserLogs(
        string $userType,
        string $identifier,
        ?string $filename = null,
        array $filters = [],
        int $page = 1,
        int $perPage = 25
    ): array {
        $filesToScan = [];
        if ($filename && $filename !== 'all') {
            $path = $this->getLogFilePath($filename);
            if ($path) {
                $filesToScan[] = $path;
            }
        } else {
            foreach ($this->getLogFiles() as $fileInfo) {
                $filesToScan[] = $fileInfo['path'];
            }
        }

        $userTypeLower = strtolower(trim($userType));
        $identifierLower = mb_strtolower(trim($identifier));
        $filterEvent = !empty($filters['event']) ? strtoupper(trim($filters['event'])) : null;
        $filterKeyword = !empty($filters['keyword']) ? mb_strtolower(trim($filters['keyword'])) : null;

        $matched = [];
        $uniqueIps = [];
        $eventBreakdown = [];
        $firstSeen = null;
        $lastSeen = null;
        $detectedUserInfo = [
            'user_type' => $userTypeLower,
            'user_id' => is_numeric($identifier) ? (int) $identifier : null,
            'user_name' => null,
            'user_email' => null,
        ];

        foreach ($filesToScan as $filePath) {
            $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false || empty($lines)) {
                continue;
            }

            foreach ($lines as $line) {
                $record = json_decode($line, true);
                if (!is_array($record)) {
                    continue;
                }

                $recordUserType = strtolower($record['user_type'] ?? 'guest');
                $recordUserId = (string) ($record['user_id'] ?? '');
                $recordUserEmail = mb_strtolower(trim($record['user_email'] ?? ''));
                $recordIp = trim($record['ip'] ?? '');
                $recordUserName = trim($record['user_name'] ?? '');

                // Check if this record belongs to the requested user
                $isMatch = false;
                if ($userTypeLower === 'customer') {
                    if ($recordUserType === 'customer' && ($recordUserId === $identifier || $recordUserEmail === $identifierLower)) {
                        $isMatch = true;
                    }
                } elseif ($userTypeLower === 'admin') {
                    if ($recordUserType === 'admin' && ($recordUserId === $identifier || $recordUserEmail === $identifierLower || mb_strtolower($recordUserName) === $identifierLower)) {
                        $isMatch = true;
                    }
                } else { // guest
                    if ($recordUserType === 'guest' && ($recordIp === $identifier || $recordUserEmail === $identifierLower || mb_strtolower($recordUserName) === $identifierLower)) {
                        $isMatch = true;
                    }
                }

                // If identifier is an email and matches regardless of user_type
                if (!$isMatch && filter_var($identifier, FILTER_VALIDATE_EMAIL) && $recordUserEmail === $identifierLower) {
                    $isMatch = true;
                }

                if (!$isMatch) {
                    continue;
                }

                // Extract latest user profile info from records
                if (!$detectedUserInfo['user_name'] && !empty($record['user_name']) && $record['user_name'] !== 'Guest') {
                    $detectedUserInfo['user_name'] = $record['user_name'];
                }
                if (!$detectedUserInfo['user_email'] && !empty($record['user_email'])) {
                    $detectedUserInfo['user_email'] = $record['user_email'];
                }
                if (!$detectedUserInfo['user_id'] && !empty($record['user_id'])) {
                    $detectedUserInfo['user_id'] = $record['user_id'];
                }

                // Track stats across all matched records
                $ev = $record['event'] ?? 'UNKNOWN';
                $eventBreakdown[$ev] = ($eventBreakdown[$ev] ?? 0) + 1;

                if (!empty($record['ip'])) {
                    $uniqueIps[$record['ip']] = ($uniqueIps[$record['ip']] ?? 0) + 1;
                }

                $ts = $record['timestamp'] ?? null;
                if ($ts) {
                    if ($lastSeen === null || $ts > $lastSeen) {
                        $lastSeen = $ts;
                    }
                    if ($firstSeen === null || $ts < $firstSeen) {
                        $firstSeen = $ts;
                    }
                }

                // Filter by event type if requested
                if ($filterEvent !== null && $ev !== $filterEvent) {
                    continue;
                }

                // Filter by keyword if requested
                if ($filterKeyword !== null) {
                    $searchContent = mb_strtolower(
                        ($record['url'] ?? '') . ' ' .
                        ($record['route'] ?? '') . ' ' .
                        ($record['ip'] ?? '') . ' ' .
                        json_encode($record['details'] ?? [])
                    );
                    if (mb_strpos($searchContent, $filterKeyword) === false) {
                        continue;
                    }
                }

                $matched[] = $record;
            }
        }

        // Sort all matched events descending by timestamp (newest first)
        usort($matched, function ($a, $b) {
            return strcmp($b['timestamp'] ?? '', $a['timestamp'] ?? '');
        });

        $total = count($matched);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;
        $items = array_slice($matched, $offset, $perPage);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'user_info' => $detectedUserInfo,
            'stats' => [
                'total_events' => array_sum($eventBreakdown),
                'filtered_total' => $total,
                'event_breakdown' => $eventBreakdown,
                'unique_ips' => array_keys($uniqueIps),
                'first_seen' => $firstSeen,
                'last_seen' => $lastSeen,
            ],
        ];
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return round($bytes / 1048576, 2) . ' MB';
    }
}
