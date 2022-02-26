<?php


namespace ModStart\Data;


use ModStart\Core\Util\PathUtil;

abstract class AbstractRemoteDataStorage extends AbstractDataStorage
{
    protected $remoteType = '';

    public function driverName()
    {
        return $this->remoteType;
    }


    public function domain()
    {
        return modstart_config()->getWithEnv($this->remoteType . '_Domain');
    }

    public function domainInternal()
    {
        return modstart_config()->getWithEnv($this->remoteType . '_DomainInternal', modstart_config()->getWithEnv($this->remoteType . '_Domain'));
    }

    public function updateDriverDomain($data)
    {
        $update = [
            'driver' => $this->remoteType,
            'domain' => $this->domain(),
        ];
        $this->repository->updateData($data['id'], $update);
        return array_merge($data, $update);
    }

    public function getDriverFullPath($path)
    {
        $path = parent::getDriverFullPath($path);
        if (PathUtil::isPublicNetPath($path)) {
            return $path;
        }
        return $this->domain() . $path;
    }

    public function getDriverFullPathInternal($path)
    {
        $path = parent::getDriverFullPath($path);
        if (PathUtil::isPublicNetPath($path)) {
            return $path;
        }
        return $this->domainInternal() . $path;
    }
}
