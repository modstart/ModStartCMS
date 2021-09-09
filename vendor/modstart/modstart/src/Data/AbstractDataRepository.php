<?php


namespace ModStart\Data;


abstract class AbstractDataRepository
{
    abstract public function maxFilenameByte();

    abstract public function addTemp($category, $path, $filename, $size);

    abstract public function getTemp($category, $path);

    abstract public function getTempByPath($dataTempPath);

    abstract public function deleteTempById($id);

    abstract public function addData($category, $path, $filename, $size);

    abstract public function updateData($dataId, $update);

    abstract public function getDataById($id);

    abstract public function getDataByPath($path);

    abstract public function deleteDataById($id);
}