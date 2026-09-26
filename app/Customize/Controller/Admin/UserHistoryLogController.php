<?php

/*
 * User History Log Admin Controller
 * Provides an administrative interface to monitor, filter, search, download, and clear user activity logs.
 */

namespace Customize\Controller\Admin;

use Customize\Service\UserHistoryLogger;
use Eccube\Controller\AbstractController;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserHistoryLogController extends AbstractController
{
    private UserHistoryLogger $logger;
    private CustomerRepository $customerRepository;
    private MemberRepository $memberRepository;

    public function __construct(
        UserHistoryLogger $logger,
        CustomerRepository $customerRepository,
        MemberRepository $memberRepository
    ) {
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->memberRepository = $memberRepository;
    }

    /**
     * Display the private User History Log viewer page.
     *
     * @Route("/%eccube_admin_route%/setting/system/user_history", name="admin_setting_system_user_history", methods={"GET"})
     * @Route("/%eccube_admin_route%/log/user_history", name="admin_log_user_history", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $files = $this->logger->getLogFiles();
        $defaultFile = $this->logger->getDefaultLogFileName();
        $selectedFile = $request->query->get('file', $defaultFile);

        // Ensure selected file exists or fall back to default
        $filePath = $this->logger->getLogFilePath($selectedFile);
        if (!$filePath && !empty($files)) {
            $selectedFile = $files[0]['filename'];
            $filePath = $files[0]['path'];
        }

        $filters = [
            'event' => $request->query->get('event', ''),
            'user_type' => $request->query->get('user_type', ''),
            'keyword' => $request->query->get('keyword', ''),
            'status_code' => $request->query->get('status_code', ''),
        ];

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = max(10, min(200, (int) $request->query->get('per_page', 25)));

        $logsData = $this->logger->getLogs($selectedFile, $filters, $page, $perPage);
        $stats = $this->logger->getStats($selectedFile);

        return $this->render('@admin/Setting/System/user_history.twig', [
            'files' => $files,
            'current_file' => $selectedFile,
            'filters' => $filters,
            'logs' => $logsData,
            'stats' => $stats,
            'per_page' => $perPage,
        ]);
    }

    /**
     * Download log file as CSV or raw text.
     *
     * @Route("/%eccube_admin_route%/setting/system/user_history/download", name="admin_setting_system_user_history_download", methods={"GET"})
     */
    public function download(Request $request): Response
    {
        $file = $request->query->get('file', $this->logger->getDefaultLogFileName());
        $format = $request->query->get('format', 'csv');

        $filePath = $this->logger->getLogFilePath($file);
        if (!$filePath) {
            $this->addError('admin.common.file_not_found', 'admin');
            return $this->redirectToRoute('admin_setting_system_user_history');
        }

        if ($format === 'raw') {
            $response = new BinaryFileResponse($filePath);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                basename($filePath)
            );
            $response->headers->set('Content-Type', 'text/plain; charset=UTF-8');
            return $response;
        }

        // CSV export with active filters applied
        $filters = [
            'event' => $request->query->get('event', ''),
            'user_type' => $request->query->get('user_type', ''),
            'keyword' => $request->query->get('keyword', ''),
            'status_code' => $request->query->get('status_code', ''),
        ];

        $csvData = $this->logger->exportCsv($file, $filters);
        $csvFilename = str_replace('.log', '.csv', basename($filePath));

        $response = new Response($csvData);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $csvFilename
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * Clear / truncate a selected log file.
     *
     * @Route("/%eccube_admin_route%/setting/system/user_history/clear", name="admin_setting_system_user_history_clear", methods={"POST"})
     */
    public function clear(Request $request): Response
    {
        $this->isTokenValid();

        $file = $request->request->get('file', '');
        if ($this->logger->clearLog($file)) {
            $this->addSuccess('admin.setting.system.user_history_log.log_cleared', 'admin');
        } else {
            $this->addError('admin.common.operation_error', 'admin');
        }

        return $this->redirectToRoute('admin_setting_system_user_history', ['file' => $file]);
    }

    /**
     * Generate sample activity records for immediate testing/demo.
     *
     * @Route("/%eccube_admin_route%/setting/system/user_history/generate_sample", name="admin_setting_system_user_history_generate_sample", methods={"POST"})
     */
    public function generateSample(Request $request): Response
    {
        $this->isTokenValid();

        $this->logger->generateSampleData();
        $this->addSuccess('admin.setting.system.user_history_log.sample_generated', 'admin');

        $defaultFile = $this->logger->getDefaultLogFileName();
        return $this->redirectToRoute('admin_setting_system_user_history', ['file' => $defaultFile]);
    }

    /**
     * Display dedicated history timeline and profile for an individual user.
     *
     * @Route("/%eccube_admin_route%/setting/system/user_history/user/{user_type}/{identifier}", name="admin_setting_system_user_history_user", methods={"GET"})
     * @Route("/%eccube_admin_route%/log/user/{user_type}/{identifier}", name="admin_log_user_history_user", methods={"GET"})
     */
    public function userDetail(string $user_type, string $identifier, Request $request): Response
    {
        $files = $this->logger->getLogFiles();
        $selectedFile = $request->query->get('file', 'all');

        $filters = [
            'event' => $request->query->get('event', ''),
            'keyword' => $request->query->get('keyword', ''),
        ];

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = max(10, min(200, (int) $request->query->get('per_page', 25)));

        $userData = $this->logger->getUserLogs($user_type, $identifier, $selectedFile, $filters, $page, $perPage);

        // Fetch database entity if available (Customer or Member)
        $customer = null;
        $member = null;

        if (strtolower($user_type) === 'customer') {
            if (is_numeric($identifier)) {
                $customer = $this->customerRepository->find((int) $identifier);
            }
            if (!$customer && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $customer = $this->customerRepository->findOneBy(['email' => $identifier]);
            }
        } elseif (strtolower($user_type) === 'admin') {
            if (is_numeric($identifier)) {
                $member = $this->memberRepository->find((int) $identifier);
            }
            if (!$member) {
                $member = $this->memberRepository->findOneBy(['login_id' => $identifier]);
            }
        }

        return $this->render('@admin/Setting/System/user_history_detail.twig', [
            'user_type' => $user_type,
            'identifier' => $identifier,
            'customer' => $customer,
            'member' => $member,
            'user_info' => $userData['user_info'],
            'stats' => $userData['stats'],
            'logs' => $userData,
            'files' => $files,
            'selected_file' => $selectedFile,
            'filters' => $filters,
            'per_page' => $perPage,
        ]);
    }

    /**
     * Download CSV logs for this specific user.
     *
     * @Route("/%eccube_admin_route%/setting/system/user_history/user/{user_type}/{identifier}/download", name="admin_setting_system_user_history_user_download", methods={"GET"})
     */
    public function downloadUser(string $user_type, string $identifier, Request $request): StreamedResponse
    {
        $selectedFile = $request->query->get('file', 'all');
        $filters = [
            'event' => $request->query->get('event', ''),
            'keyword' => $request->query->get('keyword', ''),
        ];

        // Fetch all matching records without pagination
        $userData = $this->logger->getUserLogs($user_type, $identifier, $selectedFile, $filters, 1, 10000);
        $items = $userData['items'] ?? [];

        $cleanIdentifier = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $identifier);
        $filename = sprintf('user_history_%s_%s_%s.csv', $user_type, $cleanIdentifier, date('Ymd_His'));

        $response = new StreamedResponse(function () use ($items) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'Log ID',
                'Timestamp',
                'Event Type',
                'User Type',
                'User ID',
                'User Name',
                'User Email',
                'IP Address',
                'Region',
                'HTTP Method',
                'Route',
                'URL',
                'Status Code',
                'Referer',
                'User Agent',
                'Action Details (JSON)',
            ]);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item['id'] ?? '',
                    $item['timestamp'] ?? '',
                    $item['event'] ?? '',
                    $item['user_type'] ?? '',
                    $item['user_id'] ?? '',
                    $item['user_name'] ?? '',
                    $item['user_email'] ?? '',
                    $item['ip'] ?? '',
                    $item['region'] ?? '',
                    $item['method'] ?? '',
                    $item['route'] ?? '',
                    $item['url'] ?? '',
                    $item['status_code'] ?? '',
                    $item['referer'] ?? '',
                    $item['user_agent'] ?? '',
                    json_encode($item['details'] ?? [], JSON_UNESCAPED_UNICODE),
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
