import {ExcelReader, ExcelWriter} from "@ModStartAsset/svue/lib/excel-util";
import {FileUtil} from '@ModStartAsset/svue/lib/file-util'

if (!('MS' in window)) {
    window.MS = {};
}

window.MS.file = {
    excelWriter: ExcelWriter,
    excelReader: ExcelReader,
    util: FileUtil
}

