<?php


namespace ModStart\Data\Storage;

use ModStart\Core\Input\Response;
use ModStart\Data\AbstractDataStorage;
use ModStart\Data\Event\DataFileUploadedEvent;

class FileDataStorage extends AbstractDataStorage
{
    public function init()
    {

    }

    public function get($file)
    {
        if ($this->localStorage->has($file)) {
            return $this->localStorage->get($file);
        }
        return null;
    }

    public function size($file)
    {
        return $this->localStorage->size($file);
    }

    public function put($file, $content)
    {
        $this->localStorage->put($file, $content);
    }

    public function has($file)
    {
        return $this->localStorage->has($file);
    }

    public function delete($file)
    {
        $this->localStorage->delete($file);
    }

    public function move($from, $to)
    {
        $this->localStorage->copy($from, $to);
        $this->localStorage->delete($from);
    }

    public function multiPartInit($param)
    {
        $token = $this->multiPartInitToken($param);
        $this->uploadChunkTokenAndUpdateToken($token);
        return Response::generate(0, 'ok', $token);
    }

    public function multiPartUpload($param)
    {
        $token = $this->multiPartInitToken($param);
        $input = $param['input'];
        $category = $param['category'];
        $data = [];
        if (!isset($input['chunks'])) {
            $input['chunks'] = 1;
        }
        if (!isset($input['chunk'])) {
            $input['chunk'] = 0;
        }
        if (empty($input['file'])) {
            return Response::generateError('MultiPartUpload file empty');
        }
        $data['chunks'] = $input['chunks'];
        $data['chunk'] = $input['chunk'];
        $data['file'] = $input['file'];

        // if ($data['chunk'] == 3 && rand(0, 10) > 2) {
        //     return Response::generateError('ShouldRetryUpload');
        // }

        $hashFile = self::DATA_CHUNK . '/data/' . $token['hash'];
        if ($data['chunk'] < $data['chunks']) {
            $p = $data['file']->getRealPath();
            if (false === $p) {
                $p = $data['file']->getPathname();
            }
            $content = file_get_contents($p);
            $this->localStorage->put($hashFile . '.' . $data['chunk'], $content);
            $token['chunkUploaded'] = $data['chunk'] + 1;
            $this->uploadChunkTokenAndUpdateToken($token);
            $data['finished'] = false;
            if ($token['chunkUploaded'] == $data['chunks']) {
                $this->combine($hashFile);
                $this->uploadChunkTokenAndDeleteToken($token);
                $hashFileSize = $this->size($hashFile);
                if ($hashFileSize != $token['size']) {
                    return Response::generate(-1, 'MultiPartUpload combile file failed (' . $hashFileSize . ',' . $token['size'] . ') ShouldRetryUpload');
                }
                $this->move($hashFile, $token['fullPath']);
                DataFileUploadedEvent::fire(null, $category, $token['fullPath']);
                $dataTemp = $this->repository->addTemp($category, $token['path'], $token['name'], $token['size'], empty($token['md5']) ? null : $token['md5']);
                $data['data'] = $dataTemp;
                $data['path'] = $token['fullPath'];
                $data['preview'] = $this->getDriverFullPath($token['fullPath']);
                $data['finished'] = true;
            }
        }
        return Response::generate(0, 'ok', $data);
    }

    private function combine($file)
    {
        $root = config('filesystems.disks.data.root');
        $out = @fopen($root . $file, "wb");
        if (flock($out, LOCK_EX)) {
            for ($i = 0; ; $i++) {
                if (!$this->localStorage->has($file . '.' . $i)) {
                    break;
                }
                $content = file_get_contents($root . $file . '.' . $i);
                fwrite($out, $content);
                @unlink($root . $file . '.' . $i);
            }
            flock($out, LOCK_UN);
        }
        fclose($out);
    }

}
