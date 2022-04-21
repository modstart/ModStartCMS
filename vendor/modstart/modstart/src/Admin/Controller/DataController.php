<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Data\FileManager;
use ModStart\Data\UeditorManager;

class DataController extends Controller
{
    public function fileManager($category)
    {
        if (Request::isPost()) {
            return FileManager::handle(
                $category,
                'admin_upload', 'admin_upload_category',
                Admin::id(),
                null,
                function ($action) {
                    switch ($action) {
                        case 'config':
                            break;
                        case 'categoryDelete':
                        case 'fileDelete':
                            AdminPermission::demoCheck();
                            AdminPermission::permitCheck('DataFileManagerDelete');
                            break;
                        case 'init':
                        case 'upload':
                        case 'save':
                        case 'saveRaw':
                        case 'uploadAndSaveBase64':
                        case 'uploadDirect':
                        case 'uploadDirectRaw':
                            AdminPermission::demoCheck();
                            AdminPermission::permitCheck('DataFileManagerUpload');
                            break;
                        case 'fileEdit':
                        case 'categoryEdit':
                            AdminPermission::demoCheck();
                            AdminPermission::permitCheck('DataFileManagerAdd/Edit');
                            break;
                        case 'list':
                        case 'category':
                            AdminPermission::permitCheck('DataFileManagerView');
                            break;
                        default:
                            Response::quit(-1, 'Data Permit Denied');
                            break;
                    }
                }
            );
        }
        return view('modstart::admin.data.fileManager', [
            'category' => $category,
            'pageTitle' => L('Select ' . ucfirst($category)),
        ]);
    }

    public function ueditor()
    {
        return UeditorManager::handle('admin_upload', 'admin_upload_category', Admin::id());
    }
}
