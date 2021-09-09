import {ExcelReader, ExcelWriter} from "../../lib/excel-util";
import {ListDispatcher, ListCollector} from "../../lib/batch-util";
import {FileUtil} from "../../lib/file-util";

export const ExcelExportMixin = {
    data() {
        return {
            exportName: 'export',
            exportHeadTitles: [],
            exportFinishCallback: null,
        }
    },
    methods: {
        doExportProcessExecute(fetchCB) {
            const loading = this.$loading({
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
                    this.$dialog.tipError('下载数据错误：' + msg);
                    loading.close();
                })
                .interval(0)
                .fetch((cb, me) => {
                    fetchCB(page++, res => {
                        if (res.code === 0) {
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
                    if (null === this.exportFinishCallback) {
                        let records = []
                        records.push(this.exportHeadTitles)
                        new ExcelWriter().data(records.concat(data)).filename(`${this.exportName}.xlsx`).download()
                        setTimeout(() => {
                            loading.close()
                        }, 2000)
                    } else {
                        this.exportFinishCallback(data, () => {
                            setTimeout(() => {
                                loading.close()
                            }, 2000)
                        })
                    }
                })
                .start()
        },
        doExportProcess(fetchCB) {
            this.$dialog.confirm(`确认导出当前列表？`, res => {
                this.doExportProcessExecute(fetchCB)
            })
        },
    }
}

export const ExcelImportMixin = {
    data() {
        return {
            headTitles: ['列1', '列2', '列3', '列4'],
            headCheck: null,
            max: 10000,
            importSuccess: 0,
            importError: 0,
            importTotal: 0,
            chunk: 100,
            processName: '上传',
            parseFinishedCallback: null,
            templateFileName: 'template',
        }
    },
    methods: {
        doDownloadTemplate(type) {
            type = type || 'xlsx'
            switch (type) {
                case 'csv':
                    this.doDownloadTemplateData(type, data => {
                        new ExcelWriter()
                            .data(data)
                            .filename(`${this.templateFileName}.csv`)
                            .download()

                    })
                    break
                case 'txt':
                    this.doDownloadTemplateData(type, data => {
                        let lines = []
                        data.forEach(o => {
                            lines.push(o.join(","))
                        })
                        FileUtil.download(`${this.templateFileName}.txt`, lines.join("\n"))
                    })
                    break
                default:
                    this.doDownloadTemplateData(type, data => {
                        new ExcelWriter()
                            .data(data)
                            .filename(`${this.templateFileName}.xlsx`)
                            .download()
                    })
                    break
            }
        },
        // 自定义数据过滤
        dataFilter(data) {
            return data
        },
        headCheckDefault(data) {
            if (JSON.stringify(this.headTitles) !== JSON.stringify(data[0])) {
                return '文件格式不正确，第一行必须为：' + this.headTitles.join(',')
            }
            return null
        },
        parse(processCB, finishCB) {
            this.showMask()
            const upload = (data) => {
                if (this.max >= 0 && data.length > this.max) {
                    this.$dialog.tipError(`最大${this.processName} ${this.max} 条记录`)
                    this.clear()
                    this.hideMask()
                    return
                }
                if (data.length < 1) {
                    this.$dialog.tipError(`${this.processName}数据为空`)
                    this.clear()
                    this.hideMask()
                    return
                }
                let msg = null
                switch (this.headCheck) {
                    case 'none':
                        break;
                    case 'custom':
                        msg = this.headCheckCustom(data)
                        break
                    default:
                        msg = this.headCheckDefault(data)
                        break
                }
                if (null !== msg) {
                    this.$dialog.tipError(msg)
                    this.clear()
                    this.hideMask()
                    return
                }
                if ('none' !== this.headCheck) {
                    data.shift()
                }
                data = this.dataFilter(data)
                if (data.length <= 0) {
                    this.$dialog.tipError(`${this.processName}过滤后为空，请检查数据是否正确`)
                    this.clear()
                    this.hideMask()
                    return
                }
                this.importSuccess = 0
                this.importError = 0
                this.importTotal = data.length

                new ListDispatcher()
                    .set(data)
                    .chunk(this.chunk)
                    .error((msg, me) => {
                        this.$dialog.tipError(`${this.processName}数据错误：${msg}`);
                        this.clear()
                        this.hideMask()
                    })
                    .interval(0)
                    .dispatch((list, cb, me) => {
                        this.loading.text = `数据${this.processName}中（` + (this.importSuccess + this.importError) + '/' + this.importTotal + '）'
                        processCB(list, res => {
                            if (0 === res.code) {
                                this.importSuccess += res.success
                                this.importError += res.error
                                cb({code: 0, msg: null})
                            } else {
                                cb({code: -1, msg: res.msg})
                            }
                        })
                    })
                    .finish((me) => {
                        this.loading.spinner = 'el-icon-success'
                        this.loading.text = `数据${this.processName}完成（成功 ${this.importSuccess} 条，失败 ${this.importError} 条）`
                        this.clear()
                        finishCB && finishCB()
                        if (null !== this.parseFinishedCallback) {
                            setTimeout(() => {
                                this.hideMask()
                                this.parseFinishedCallback(me)
                            }, 2000)
                        } else {
                            this.$emit('update')
                            setTimeout(() => {
                                this.hideMask()
                                this.$dialog.alert(`数据${this.processName}完成（成功 ${this.importSuccess} 条，失败 ${this.importError} 条）`)
                            }, 2000)
                        }
                    })
                    .start()
            }
            setTimeout(() => {
                switch (this.fileType) {
                    case 'txt':
                        new ExcelReader().file(this.file).parse(function (data) {
                            upload(data)
                        })
                        break
                    case 'xlsx':
                    case 'xls':
                    case 'csv':
                        new ExcelReader().file(this.file).parse(function (data) {
                            upload(data)
                        })
                        break
                }
            }, 100)
        }
    }
}
export const ImportMixin = {
    data() {
        return {
            file: null,
            fileName: null,
            fileType: null,
            loading: null,
            importProcessName: '导入',
        }
    },
    methods: {
        onFileSelect(file) {
            this.file = file.raw
            this.fileName = this.file.name
            this.fileType = this.fileName.substr(this.fileName.lastIndexOf('.') + 1).toLowerCase()
            this.doFileSelect()
        },
        clear() {
            this.$refs.upload.clearFiles()
            this.file = null
        },
        showMask() {
            if (this.loading) {
                return
            }
            this.loading = this.$loading({
                lock: true,
                text: `正在${this.importProcessName}`,
                spinner: 'el-icon-loading',
                background: 'rgba(0, 0, 0, 0.7)'
            })
        },
        updateMask(processed, total) {
            this.loading.text = `数据${this.importProcessName}中（` + processed + '/' + total + '）'
        },
        hideMask() {
            this.loading.close()
            this.loading = null
        }
    }
}


export const EditMixin = {
    data() {
        return {
            submitLoading: false,
            isInit: false,
            data: {
                id: 0
            }
        }
    },
    methods: {
        initPrepare() {
            for (let i = 0; i < arguments.length; i++) {
                if (i === arguments.length - 1) {
                    if (this.isInit) {
                        return
                    }
                    this.isInit = true
                    arguments[i]()
                } else {
                    if (!arguments[i]) {
                        setTimeout(() => {
                            this.init()
                        }, 100)
                        return
                    }
                }
            }
        },
        init() {
            console.log('implements init ( page init once, for example : lazy value )')
        },
        ensureInit(cb) {
            if (this.isInit) {
                cb && cb()
            } else {
                setTimeout(() => {
                    this.ensureInit(cb)
                }, 100)
            }
        },
        doSubmit() {
            console.log('implements doSubmit', JSON.stringify(this.data))
        },
        doSubmitProcess(url, param, successCB, errorCB) {
            this.submitLoading = true
            this.$api.post(url, Object.assign(this.data, param), res => {
                this.submitLoading = false
                this.$dialog.tipSuccess('保存成功')
                this.$emit('update')
                successCB && successCB(res)
            }, res => {
                this.submitLoading = false
                errorCB && errorCB(res)
            })
        },
        doAdd() {
            console.log('implements doAdd')
        },
        doEdit(id) {
            console.log('implements doEdit', JSON.stringify(id))
        }
    }
}


export const DeleteMixin = {
    data() {
        return {
            deleteLoading: false,
        }
    },
    methods: {
        doDelete(id) {
            console.log('implements doDelete', JSON.stringify(this.data))
        },
        doDeleteProcess(id, url, param, successCB, errorCB) {
            this.$dialog.confirm('确定删除？', res => {
                this.deleteLoading = true
                this.$api.post(url, Object.assign({id: id}, param), res => {
                    this.deleteLoading = false
                    this.$dialog.tipSuccess('删除成功')
                    this.$emit('update')
                    successCB && successCB(res)
                }, res => {
                    this.deleteLoading = false
                    errorCB && errorCB(res)
                })
            })
        },
    }
}


export const InitPrepareMixin = {
    data() {
        return {
            isInit: false,
        }
    },
    methods: {
        initPrepare() {
            for (let i = 0; i < arguments.length; i++) {
                if (i === arguments.length - 1) {
                    if (this.isInit) {
                        return
                    }
                    this.isInit = true
                    arguments[i]()
                } else {
                    if (!arguments[i]) {
                        setTimeout(() => {
                            this.init()
                        }, 100)
                        return
                    }
                }
            }
        },
        init() {
            console.log('implements init ( page init once, for example : lazy value )')
        },
    }
}
