<?php

declare(strict_types=1);

namespace App\Service\Admin\Action;

use App\Utils\Aliyun\OssUtil;
use Hyperf\Filesystem\FilesystemFactory;
use Vtiful\Kernel\Excel;

class ExcelExport
{
    public function __construct(protected FilesystemFactory $factory) {}

    public function exportToOss(string $fileName, array $headers, callable $dataGenerator): array
    {
        $fullDir = BASE_PATH . '/runtime/exports';
        !is_dir($fullDir) && mkdir($fullDir, 0755, true);

        $excel = new Excel(['path' => $fullDir]);
        $sheet = $excel->fileName($fileName)->checkoutSheet('Sheet1');
        $sheet->header($headers);

        // 执行回调写入数据
        $rowCount = $dataGenerator($sheet);
        $filePath = $sheet->output();

        try {
            $oss = $this->factory->get('oss');
            $ossTargetName = OssUtil::rename($fileName, '');

            $stream = fopen($filePath, 'r');
            $oss->writeStream($ossTargetName, $stream);
            if (is_resource($stream)) fclose($stream);

            return [$ossTargetName, $rowCount];
        } finally {
            if (file_exists($filePath)) unlink($filePath);
        }
    }
}
