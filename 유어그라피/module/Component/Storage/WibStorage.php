<?php
namespace Component\Storage;

use Framework\StaticProxy\Proxy\Request;
use Framework\StaticProxy\Proxy\UserFilePath;
use League\Flysystem\Adapter\Local;

/**
 * 기존에 사용중인 LOCALSTORAGE COMPONENT를 활용해서 저장위치만 바꿔서 사용함
 * 파일 처리부분은 공용이라 패치시 위험도가 높아서 따로 빼서 사용하고 있으니 추후 오류 발생시
 * 원본 LOCALSTORAGE COMPONENT참고해서 수정해야됨
 */
class WibStorage extends \Component\Storage\AbstractStorage
{
    protected $realPath;

    public function __construct($pathCode, $storageName)
    {
        $basePath = $this->getDiskPath($pathCode);
        $newbasePath = explode('/data/', $basePath);
        
        $uploadDir = '/data/wibUpload/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $this->storageName = $storageName;
        $this->basePath = $uploadDir;
        $this->realPath = $newbasePath[0].$uploadDir;
        $this->setAdapter();
    }

    protected function setAdapter()
    {
        parent::__construct(new Local((string)$this->realPath));
    }

    public function getPath($pathCodeDirName)
    {
        return UserFilePath::data($pathCodeDirName);
    }

    public function isFile($filePath)
    {
        return is_file(realpath($this->getRealPath($filePath)));
    }

    public function getHttpPath($filePath)
    {
        $result = $this->basePath . DS . $filePath;
        return $result;
    }

    public function getFilename($filePath)
    {
        if (substr($filePath, -1) != DS || substr($filePath, -1) != '\\') {
            return basename($filePath);
        }

        return null;
    }

    public function getMountPath($filePath)
    {
        return $filePath;
    }

    public function getRealPath($filePath)
    {
        return $this->getAdapter()->getPathPrefix() . $filePath;
    }

    public function getDownLoadPath($filePath)
    {
        return $this->getRealPath($filePath);
    }

    final public function download($filePath, $downloadFilename)
    {
        $realPath = $this->getRealPath($filePath);
        parent::setDownloadHeader($realPath, $downloadFilename);
    }

    public function isFileExists($filePath)
    {
        return file_exists(realpath($this->getRealPath($filePath)));
    }

    public function delete($filePath)
    {
        //if ($this->isFile($filePath)) {
        if ($this->isFileExists($filePath)) {
            return parent::delete($filePath);
        }

        return false;
    }

    public function rename($oldFilePath, $newFilePath)
    {
        return @rename($oldFilePath, $newFilePath);
    }

}