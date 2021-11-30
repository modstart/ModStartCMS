import {Storage} from "../../lib/storage";
import {ViewUtil} from "../../lib/view"

export const ListerMixin = {
    data() {
        return {
            tableHeight: 500,

            listLoading: false,
            listActionName: '',
            listActionStatus: {},
            selectIds: [],
            list: {
                page: 1,
                pageSize: 10,
                records: [],
                total: 0,
                maxRecords: -1,
            },
            search: {},
            order: {
                field: null,
                type: 'asc',
            },
            filter: [],
        }
    },
    computed: {
        isAllChecked() {
            if (this.list.records.length > 0) {
                if (this.list.records.length === this.list.records.filter(o => o._checked).length) {
                    return true
                }
            }
            return false
        },
        checkedIds() {
            return this.list.records.filter(o => o._checked).map(o => o.id)
        },
        checkedRecords() {
            return this.list.records.filter(o => o._checked)
        }
    },
    methods: {
        setHeightResponsively(minHeight, heightCalculator) {
            ViewUtil.setHeightResponsively(this, 'tableHeight', minHeight, heightCalculator)
        },
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
        doList(page) {
            console.error('should implements doList')
        },
        doListProcess(url, page, param, successCB, errorCB, resSuccessPreprocessCB) {
            param = param || {}
            page = page || this.list.page
            this.list.page = page
            this.listLoading = true
            this.$api.post(url, Object.assign({
                order: this.order,
                search: this.search,
                filter: this.filter,
                page: this.list.page,
                pageSize: this.list.pageSize
            }, param), res => {
                this.listLoading = false
                if (resSuccessPreprocessCB) {
                    res = resSuccessPreprocessCB(res)
                }
                this.list.page = res.data.page
                this.list.pageSize = res.data.pageSize
                this.list.records = res.data.records.map(o => {
                    o._checked = false
                    return o
                })
                this.list.total = res.data.total
                successCB && successCB(res)
            }, res => {
                this.listLoading = false
                errorCB && errorCB()
            })
        },
        doListProcessCustom(url, page, param, successCB, errorCB) {
            param = param || {}
            this.$api.post(url, Object.assign({
                order: this.order,
                search: this.search,
                filter: this.filter,
                page: page,
                pageSize: this.list.pageSize
            }, param), res => {
                successCB && successCB(res)
            }, res => {
                errorCB && errorCB()
            })
        },
        doListProcessRaw(url, param, successCB, errorCB) {
            param = param || {}
            this.listLoading = true
            this.$api.post(url, param, res => {
                this.listLoading = false
                successCB && successCB(res)
            }, res => {
                this.listLoading = false
                errorCB && errorCB()
            })
        },
        doDeleteAll() {
            console.error('should implements doList')
        },
        onSelectionChange(rows) {
            this.selectIds = rows.map(o => o.id)
        },
        doSelectionCancel() {
            this.$refs.lister.clearSelection()
        },
        doPageSize(pageSize) {
            this.list.pageSize = pageSize
            this.doList(1)
        },
        doSearch() {
            this.doList(1)
        },
        doSearchReset() {
            console.error('should implements doSearchReset')
        },
        doCheckAll() {
            const checked = !this.isAllChecked
            this.list.records.forEach(o => {
                o._checked = checked
            })
        },
        initListActionStatus(listActionName) {
            listActionName = listActionName || 'list'
            this.listActionName = listActionName
            let status = Storage.getObject(`ListActionStatus.${this.listActionName}`)
            if (!status) {
                status = {}
            }
            this.listActionStatus = status
        },
        setListActionStatus(id, action) {
            this.$set(this.listActionStatus, `${id}.${action}`, 1)
            Storage.set(`ListActionStatus.${this.listActionName}`, this.listActionStatus)
        }
    }
}
