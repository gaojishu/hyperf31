<?php

declare(strict_types=1);

namespace App\Utils\Aliyun;

use App\Enum\Admin\Files\FilesTypeEnum;
use Carbon\Carbon;
use Hyperf\Context\ApplicationContext;
use Ramsey\Uuid\Uuid;

use function Hyperf\Support\env;

class OssUtil
{
    /**
     * 生成前端 PostObject 直传签名
     * 对应 Kotlin: generatePostPolicy
     */
    public static function generatePostPolicy(): array
    {
        $id = env('OSS_ACCESS_ID');
        $key = env('OSS_ACCESS_SECRET');
        $bucket = env('OSS_BUCKET');
        $endpoint = env('OSS_ENDPOINT');
        $dir = env('OSS_URL_PREFIX', 'tmp/');
        $host = "https://{$bucket}.{$endpoint}";
        $expireSeconds = 7200;

        // 1. 计算过期时间 (ISO 8601 GMT)
        $expiration = Carbon::now('UTC')->addSeconds($expireSeconds)->format('Y-m-d\TH:i:s.v\Z');

        // 2. 构建 conditions
        $conditions = [
            ['content-length-range', 0, 1024 * 1024 * 1024], // 1GB
            ['starts-with', '$key', $dir]
        ];

        // 3. Base64 编码 Policy
        $policy = base64_encode(json_encode([
            'expiration' => $expiration,
            'conditions' => $conditions
        ]));

        // 4. HMAC-SHA1 签名
        $signature = base64_encode(hash_hmac('sha1', $policy, $key, true));

        return [
            'access_id'  => $id,
            'host'      => $host,
            'policy'    => $policy,
            'signature' => $signature,
            'expire'    => $expiration,
            'callback'  => null,
            'dir'       => $dir,
        ];
    }

    /**
     * 后端直接上传
     */
    public static function upload(string $localPath, string $originName): string
    {
        $newName = self::rename($originName, '');
        $stream = fopen($localPath, 'r+');
        self::getStorage()->writeStream($newName, $stream);
        if (is_resource($stream)) fclose($stream);
        return $newName;
    }

    /**
     * 重命名
     */
    public static function copyObject(string $sourceName, string $targetName): string
    {
        self::getStorage()->copy($sourceName, $targetName);
        return $targetName;
    }

    /**
     * 删除文件
     */
    public static function deleteObject(string $objectName)
    {
        return self::getStorage()->delete($objectName);
    }

    /**
     * 生成私有访问链接
     */
    public static function generatePresignedUrl(string $objectName, int $timeout = 3600): string
    {
        return self::getStorage()->temporaryUrl($objectName, Carbon::now()->addSeconds($timeout));
    }

    /**
     * 内部方法：获取存储驱动
     */
    private static function getStorage(string $d = 'oss')
    {
        return ApplicationContext::getContainer()
            ->get(\Hyperf\Filesystem\FilesystemFactory::class)
            ->get($d);
    }


    public static function getPathByUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = ltrim($path, '/');
        return $path;
    }

    /**
     * 逻辑重命名：upload/{type}/{date}/{uuid}.ext
     */
    public static function rename(string $objectName, string $mimeType): string
    {
        $extension = pathinfo($objectName, PATHINFO_EXTENSION);

        $type = explode('/', $mimeType)[0];

        $fileType = FilesTypeEnum::tryFrom($type);

        $fileType = $fileType ? $fileType->value : FilesTypeEnum::OHTER->value;


        $currentDate = Carbon::now()->format('Ymd');;
        $uuid = Uuid::uuid4();

        return "upload/{$fileType}/{$currentDate}/{$uuid}.{$extension}";
    }
}
