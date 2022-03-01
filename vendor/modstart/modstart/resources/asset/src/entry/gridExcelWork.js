import {VueManager} from "../lib/vue-manager";

import {ExcelReader, ExcelWriter} from "@ModStartAsset/svue/lib/excel-util";
import {ListDispatcher, ListCollector} from "@ModStartAsset/svue/lib/batch-util";

window.__gridExcelWork = {
    ExcelReader,
    ExcelWriter,
    ListDispatcher,
    ListCollector
}
