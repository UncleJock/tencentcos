<?php

namespace bangmoo\tencentcos;

use Qcloud\Cos\Client;
use RuntimeException;

class tencentcos
{

    private $cosClient;
    private $appId;
    private $bucket;
    private $region;

    /**
     * tencentcos constructor.
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->appId = $option['appId'];
        $this->bucket = $option['bucket'];
        $this->region = $option['region'];
        $this->cosClient = new Client([
            'region'      => $option['region'],
            'schema'      => 'https', //协议头部，默认为http
            'credentials' => [
                'secretId'  => $option['secretId'],
                'secretKey' => $option['secretKey']
            ]
        ]);
    }

    /**
     * 创建存储桶
     * @param string $bucket_name
     * @return string
     * @author: chen
     * @time: 2020/7/3 10:53
     */
    public function createBucket($bucket_name = '')
    {
        try {
            $bucket = $bucket_name . '-' . $this->appId; //存储桶名称 格式：BucketName-APPID
            $result = $this->cosClient->createBucket(array('Bucket' => $bucket));
            //请求成功
            return $result;
        } catch (RuntimeException $e) {
            //请求失败
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * 创建存储桶
     * @return string
     * @author: chen
     * @time: 2020/7/3 10:53
     */
    public function getBucket()
    {
        try {
            return $this->cosClient->listBuckets();
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * 上传文件
     * @param string $srcPath // 本地文件绝对路径
     * @param string $key // 文件名（包含路径）
     * @return bool
     * @author: chen
     * @time: 2020/8/3 13:29
     */
    public function uploadFile(string $srcPath = '', string $key = ''): bool
    {
        try {
            $bucket = $this->bucket . '-' . $this->appId;
            $file = fopen($srcPath, 'rb');
            if ($file) {
                $this->cosClient->putObject([
                    'Bucket' => $bucket,
                    'Key'    => $key,
                    'Body'   => $file
                ]);
                return true;
            }
            return false;
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * 获取临时访问链接
     * 
     * @return mixed
     * @author: chen
     * @time: 2021/12/21 14:44
     */
    public function get_object_url(string $key):string 
    {
        try {
            $bucket = $this->bucket . '-' . $this->appId;
            $signedUrl = $this->cosClient->getObjectUrl($bucket, $key, '+10 minutes');
            // 请求成功
            return $signedUrl;
        } catch (\Exception $e) {
            // 请求失败
            throw new RuntimeException($e->getMessage());
        }
    }

}
