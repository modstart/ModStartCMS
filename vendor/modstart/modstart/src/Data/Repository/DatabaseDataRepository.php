<?php


namespace ModStart\Data\Repository;

use ModStart\Admin\Model\Data;
use ModStart\Admin\Model\DataTemp;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Data\AbstractDataRepository;
use ModStart\Data\AbstractDataStorage;

class DatabaseDataRepository extends AbstractDataRepository
{
    public function maxFilenameByte()
    {
        return 200;
    }


    public function addTemp($category, $path, $filename, $size, $md5 = null)
    {
        $data = [
            'category' => $category,
            'path' => $path,
            'filename' => $filename,
            'size' => $size,
            'md5' => $md5,
        ];
        return ModelUtil::insert(DataTemp::class, $data);
    }

    public function getTemp($category, $path)
    {
        return ModelUtil::get(DataTemp::class, [
            'category' => $category,
            'path' => $path
        ]);
    }

    public function getTempByPath($dataTempPath)
    {
        if (preg_match(AbstractDataStorage::PATTERN_DATA_TEMP, $dataTempPath, $mat)) {
            return ModelUtil::get(DataTemp::class, ['category' => $mat[1], 'path' => $mat[2]]);
        }
        return null;
    }


    public function deleteTempById($id)
    {
        ModelUtil::delete(DataTemp::class, ['id' => $id]);
    }

    public function addData($category, $path, $filename, $size, $md5 = null)
    {
        $data = [
            'category' => $category,
            'path' => $path,
            'filename' => $filename,
            'size' => $size,
            'md5' => $md5
        ];
        return ModelUtil::insert(Data::class, $data);
    }

    public function updateData($dataId, $update)
    {
        ModelUtil::update(Data::class, $dataId, $update);
    }

    public function getDataById($id)
    {
        return ModelUtil::get(Data::class, ['id' => $id]);
    }

    public function getDataByPath($path)
    {
        if (preg_match(AbstractDataStorage::PATTERN_DATA, $path, $mat)) {
            return ModelUtil::get(Data::class, ['category' => $mat[1], 'path' => $mat[2]]);
        } else if (preg_match(AbstractDataStorage::PATTERN_DATA_STRING, $path, $mat)) {
            return ModelUtil::get(Data::class, ['category' => $mat[1], 'path' => $mat[2]]);
        }
        return null;
    }

    public function deleteDataById($id)
    {
        ModelUtil::delete(Data::class, ['id' => $id]);
    }


}
