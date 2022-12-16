import {VueManager} from "../lib/vue-manager";

import {ExcelReader, ExcelWriter} from "@ModStartAsset/svue/lib/excel-util";
import {ListDispatcher, ListCollector} from "@ModStartAsset/svue/lib/batch-util";

function doExportProcessExecute(fetchCB, exportHeadTitles, exportName) {
    exportName = exportName || null
    exportHeadTitles = exportHeadTitles || null
    const loading = ELEMENT.Loading.service({
        lock: true,
        text: '正在准备',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)'
    })

    let total = 0
    let processed = 0
    let page = 1
    new ListCollector()
        .error((msg, me) => {
            MS.dialog.tipError('下载数据错误：' + msg);
            loading.close();
        })
        .interval(0)
        .fetch((cb, me) => {
            fetchCB(page++, res => {
                if (res.code === 0) {
                    if (null === exportName) {
                        exportName = res.exportName || 'Export.xlsx'
                    }
                    if (null === exportHeadTitles) {
                        exportHeadTitles = res.exportHeadTitles || []
                    }
                    processed += res.list.length
                    total = res.total
                    loading.text = '已处理（' + processed + '/' + total + '）'
                    cb({code: 0, msg: null, list: res.list, finished: res.finished})
                } else {
                    cb({code: -1, msg: res.msg})
                }
            })
        })
        .finish((data, me) => {
            let records = []
            records.push(exportHeadTitles)
            new ExcelWriter().data(records.concat(data)).filename(exportName).download()
            setTimeout(() => {
                loading.close()
            }, 2000)
        })
        .start()
}


const gridExcelWork = {
    ExcelReader,
    ExcelWriter,
    ListDispatcher,
    ListCollector,
    doExportProcessExecute
}


if (!('MS' in window)) {
    window.MS = {}
}
window.MS.gridExcelWork = gridExcelWork

window.__gridExcelWork = gridExcelWork
