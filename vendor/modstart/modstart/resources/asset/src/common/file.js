import {ExcelReader, ExcelWriter} from "@ModStartAsset/svue/lib/excel-util";
import {FileUtil} from '@ModStartAsset/svue/lib/file-util'
import {ListCollector,ListDispatcher} from '@ModStartAsset/svue/lib/batch-util'

if (!('MS' in window)) {
    window.MS = {};
}

window.MS.file = {
    excelWriter: ExcelWriter,
    excelReader: ExcelReader,
    listCollector: ListCollector,
    listDispatcher: ListDispatcher,
    util: FileUtil
}

