<?php


namespace ModStart\Data\Traits;


use ModStart\Core\Input\Response;
use ModStart\Data\Event\DataFileUploadedEvent;

trait LocalMultipartUploadTrait
{
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

        $hashFile = self::DATA_CHUNK . '/data/' . $token['hash'];
        if ($data['chunk'] < $data['chunks']) {
            $content = file_get_contents($data['file']->getRealPath());
            $this->localStorage->put($hashFile . '.' . $data['chunk'], $content);
            $token['chunkUploaded'] = $data['chunk'] + 1;
            $this->uploadChunkTokenAndUpdateToken($token);
            $data['finished'] = false;
            if ($token['chunkUploaded'] == $data['chunks']) {
                $this->combine($hashFile);
                $this->uploadChunkTokenAndDeleteToken($token);
                $hashFileSize = $this->localStorage->size($hashFile);
                if ($hashFileSize != $token['size']) {
                    return Response::generate(-1, 'MultiPartUpload combile file failed (' . $hashFileSize . ',' . $token['size'] . ') ShouldRetryUpload');
                }
                $this->saveLocalToRemote($hashFile, $token['fullPath']);
                @unlink(public_path($hashFile));
                DataFileUploadedEvent::fire($this->remoteType, $category, $token['fullPath'], isset($param['eventOpt']) ? $param['eventOpt'] : []);
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
