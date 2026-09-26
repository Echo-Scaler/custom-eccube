<?php

/*
 * User History Log Admin Controller
 * Provides an administrative interface to monitor, filter, search, download, and clear user activity logs.
 */

namespace Customize\Controller\Admin;

use Customize\Service\UserHistoryLogger;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class UserHistoryLogController extends AbstractController
{
    private UserHistoryLogger $logger;

    public function __construct(UserHistoryLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Display the private User History Log viewer page.
     *
     * @Route("/%eccube_admin_route%/setting/system/user_history", name="admin_setting_system_user_history", methods={"GET"})
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
}
