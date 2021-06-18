<?php
declare(strict_types=1);

namespace MeiKaiGsuit\GSheet;


class GFile
{
    const FILE_FIELDS_KEY_ALL = '*';
    const FILE_FIELDS_KEY_ID = 'id';
    const FILE_FIELDS_KEY_NAME = 'name';
    const FILE_FIELDS_KEY_FILE_TYPE = 'mimeType';
    const FILE_FIELDS_KEY_CREATED = 'createdTime';
    const FILE_FIELDS_KEY_MODIFIED = 'modifiedTime';
    const FILE_FIELDS_KEY_PERMISSIONS = 'permissions(emailAddress,role)';

    protected $serv;

    public function __construct(\Google_Client $client)
    {
        $this->serv = new \Google_Service_Drive($client);
    }

    /**
     * 获取google drive文件
     * @param string $file_id
     * @return \Google_Service_Drive_DriveFile
    */
    public function getFileById(string $file_id) : \Google_Service_Drive_DriveFile
    {
        return $this->serv->files->get($file_id, ['fields' => self::FILE_FIELDS_KEY_ALL]);
    }

    public function getFileModifiedTime(string $file_id): string
    {
        return $this->getFileById($file_id)->getModifiedTime();
    }

    public function getFileCreatedTime(string $file_id): string
    {
        return $this->getFileById($file_id)->getCreatedTime();
    }
}