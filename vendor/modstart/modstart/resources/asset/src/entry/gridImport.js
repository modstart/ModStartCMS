import {VueManager} from "../lib/vue-manager";

import {ExcelReader, ExcelWriter} from "@ModStartAsset/svue/lib/excel-util";
import {ListDispatcher} from "@ModStartAsset/svue/lib/batch-util";

window.__gridImport = {
    ExcelReader,
    ExcelWriter,
    ListDispatcher
}
