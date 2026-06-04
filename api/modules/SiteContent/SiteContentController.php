<?php

require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../services/SiteContentService.php';

class SiteContentController
{
    public function getPublic(): void
    {
        try {
            Response::json([
                'success' => true,
                'data' => SiteContentService::grouped(true),
            ]);
        } catch (Throwable $e) {
            Response::json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAdmin(): void
    {
        try {
            AuthMiddleware::handle();

            Response::json([
                'success' => true,
                'data' => SiteContentService::getAll(false),
            ]);
        } catch (Throwable $e) {
            Response::json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(): void
    {
        try {
            AuthMiddleware::handle();

            $content = $_POST['content'] ?? [];

            foreach ($content as $sectionKey => $fields) {
                if (!is_array($fields)) {
                    continue;
                }

                foreach ($fields as $fieldKey => $value) {
                    SiteContentService::updateValue(
                        $this->sanitizeKey((string) $sectionKey),
                        $this->sanitizeKey((string) $fieldKey),
                        is_string($value) ? trim($value) : null
                    );
                }
            }

            $this->processImages();

            Response::json([
                'success' => true,
                'message' => 'Conteúdo atualizado com sucesso.',
            ]);
        } catch (Throwable $e) {
            Response::json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    private function processImages(): void
    {
        if (empty($_FILES['images']['name']) || !is_array($_FILES['images']['name'])) {
            return;
        }

        $uploadDir = __DIR__ . '/../../../frontend/uploads/site-content/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

        foreach ($_FILES['images']['name'] as $sectionKey => $fields) {
            if (!is_array($fields)) {
                continue;
            }

            foreach ($fields as $fieldKey => $originalName) {
                if (empty($originalName)) {
                    continue;
                }

                $error = $_FILES['images']['error'][$sectionKey][$fieldKey] ?? UPLOAD_ERR_NO_FILE;
                $tmpName = $_FILES['images']['tmp_name'][$sectionKey][$fieldKey] ?? null;

                if ($error !== UPLOAD_ERR_OK || !$tmpName || !is_uploaded_file($tmpName)) {
                    continue;
                }

                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowedExt, true)) {
                    continue;
                }

                $mime = mime_content_type($tmpName);

                if (!in_array($mime, $allowedMime, true)) {
                    continue;
                }

                $section = $this->sanitizeKey((string) $sectionKey);
                $field = $this->sanitizeKey((string) $fieldKey);
                $fileName = $section . '_' . $field . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destination = $uploadDir . $fileName;

                if (move_uploaded_file($tmpName, $destination)) {
                    SiteContentService::updateValue(
                        $section,
                        $field,
                        'frontend/uploads/site-content/' . $fileName
                    );
                }
            }
        }
    }

    private function sanitizeKey(string $key): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', $key) ?: '';
    }
}
