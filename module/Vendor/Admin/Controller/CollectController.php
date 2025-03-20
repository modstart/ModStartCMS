<?php

namespace Module\Vendor\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\TimeUtil;

class CollectController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '*',
    ];

    private function getLogTimestamp($log)
    {
        // [2024-10-25 09:03:09] product.ERROR:
        // [01158] 2024-11-14 10:00:58
        if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] /', $log)) {
            return strtotime(substr($log, 1, 19));
        } else if (preg_match('/^\[\d{5}\] \d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} /', $log)) {
            return strtotime(substr($log, 7, 19));
        }
        return -1;
    }

    public function index()
    {
        $input = InputPackage::buildFromInputJson('data');
        $type = $input->getTrimString('type');
        if (!in_array($type, ['FeedbackTicket:log', 'FeedbackTicket:env'])) {
            return Response::generateError('type参数错误');
        }
        $optionInput = $input->getAsInput('option');
        $data = [];
        switch ($type) {
            case 'FeedbackTicket:log':
                $startTime = $optionInput->getDatetime('startTime');
                $endTime = $optionInput->getDatetime('endTime');
                list($startTime, $endTime) = TimeUtil::limitDatetimeRange($startTime, $endTime, [
                    'periodMax' => 24 * 3600,
                ]);
                $startTimeTs = strtotime($startTime);
                $endTimeTs = strtotime($endTime);
                $logsList = [];
                if (!AdminPermission::isDemo()) {
                    $files = FileUtil::listFiles(storage_path('logs'), '*.log');
                    foreach ($files as $f) {
                        $fName = $f['filename'];
                        $f = fopen($f['pathname'], 'r');
                        $lines = [];
                        while (!feof($f)) {
                            $line = fgets($f);
                            // [2024-10-25 09:03:09] product.ERROR:
                            // [01158] 2024-11-14 10:00:58
                            if (
                                !preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] /', $line)
                                &&
                                !preg_match('/^\[\d{5}\] \d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} /', $line)
                            ) {
                                $lines[] = $line;
                                continue;
                            }
                            if (empty($lines)) {
                                $lines[] = $line;
                                continue;
                            }
                            $lineText = trim(join('', $lines));
                            $lines = [$line];
                            $lineTime = $this->getLogTimestamp($lineText);
                            if ($lineTime > 0) {
                                if ($lineTime > $endTimeTs) {
                                    break;
                                }
                                if ($lineTime < $startTimeTs) {
                                    continue;
                                }
                            }
                            if ($fName) {
                                $logsList[] = "\n============== " . $fName . " ==============";
                                $fName = '';
                            }
                            $logsList[] = $lineText;
                        }
                        if (!empty($lines)) {
                            $lineText = trim(join('', $lines));
                            $lineTime = $this->getLogTimestamp($lineText);
                            if ($lineTime < 0 || ($lineTime >= $startTimeTs && $lineTime <= $endTimeTs)) {
                                if ($fName) {
                                    $logsList[] = "\n============== " . $fName . " ==============";
                                }
                                $logsList[] = $lineText;
                            }
                        }
                    }
                }
                $data['startTime'] = $startTime;
                $data['endTime'] = $endTime;
                $data['logs'] = join("\n", $logsList);
                break;
            case 'FeedbackTicket:env':
                if (defined('\\App\\Constant\\AppConstant::APP')) {
                    $data['app'] = \App\Constant\AppConstant::APP;
                }
                if (defined('\\App\\Constant\\AppConstant::APP_NAME')) {
                    $data['appName'] = \App\Constant\AppConstant::APP_NAME;
                }
                if (defined('\\App\\Constant\\AppConstant::VERSION')) {
                    $data['version'] = \App\Constant\AppConstant::VERSION;
                }
                break;
        }
        return Response::generateSuccessData($data);
    }
}
