<template>

    <panel-content>

        <panel-box-body>
            <div class="ub-lister-search">
                <div class="field">
                    <el-radio-group v-model="search.status">
                        <el-radio-button :label="0">全部</el-radio-button>
                        <el-radio-button :label="MemberMessageStatus.UNREAD.value">{{MemberMessageStatus.UNREAD.name}}
                        </el-radio-button>
                        <el-radio-button :label="MemberMessageStatus.READ.value">{{MemberMessageStatus.READ.name}}
                        </el-radio-button>
                    </el-radio-group>
                </div>
                <div class="field">
                    <el-button :loading="loading" @click="doList(1)"><i class="iconfont icon-search"></i> 搜索</el-button>
                </div>
                <div class="field">
                    <el-button v-if="checkedRecordsIds.length>0" @click="doRead(checkedRecordsIds)">
                        标为选中{{checkedRecordsIds.length}}条已读
                    </el-button>
                    <el-button v-if="checkedRecordsIds.length>0" @click="doDelete(checkedRecordsIds)">
                        删除选中{{checkedRecordsIds.length}}条消息
                    </el-button>
                </div>
            </div>
        </panel-box-body>

        <panel-box-body v-loading="loading">
            <table class="ub-lister-table">
                <thead>
                <tr>
                    <th width="50">
                        <el-checkbox v-model="checkAll"></el-checkbox>
                    </th>
                    <th width="200">时间</th>
                    <th>内容</th>
                    <th width="100">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="data.records.length===0" class="empty">
                    <td colspan="4">暂无数据</td>
                </tr>
                <tr v-for="(record,recordIndex) in data.records"
                    :class="{row:true,muted:MemberMessageStatus.READ.value===record.status}">
                    <td>
                        <el-checkbox v-model="data.records[recordIndex].checked"></el-checkbox>
                    </td>
                    <td class="muted-content">{{record.createTime}}</td>
                    <td class="muted-content" v-html="record.content"></td>
                    <td>
                        <a class="ub-lister-action" title="标记为已读"
                           v-if="record.status===MemberMessageStatus.UNREAD.value"
                           href="javascript:;" @click="doRead([record.id])"><i
                                class="iconfont icon-checked"></i></a>
                        <a class="ub-lister-action danger" title="删除消息" href="javascript:;"
                           @click="doDelete([record.id])"><i
                                class="iconfont icon-trash"></i></a>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4" class="page">
                        <el-pagination
                                @current-change="doList()"
                                :current-page.sync="data.page"
                                :page-size="data.pageSize"
                                layout="total,prev,pager,next,jumper"
                                :total="data.total">
                        </el-pagination>
                    </td>
                </tr>
                </tfoot>
            </table>
        </panel-box-body>

    </panel-content>
</template>

<script>
    import {MemberMessageStatus} from "../../main/constant";

    export default {
        metaInfo: {
            title: '系统通知'
        },
        components: {},
        data() {
            return {
                MemberMessageStatus,
                search: {
                    status: 0,
                },
                checkAll: false,
                data: {
                    records: [],
                    total: 0,
                    pageSize: 10,
                    page: 1,
                },
                loading: false,
            }
        },
        mounted() {
            this.doList()
        },
        computed: {
            checkedRecordsIds() {
                return this.data.records.filter(o => o.checked
                ).map(o => o.id
                )
            }
        },
        watch: {
            checkAll(newValue, oldValue) {
                if (newValue) {
                    this.data.records.map(o => o.checked = newValue
                    )
                } else {
                    let allChecked = this.data.records.filter(o => o.checked
                    ).length == this.data.records.length
                    if (allChecked) {
                        this.data.records.map(o => o.checked = newValue
                        )
                    }
                }
            },
            data: {
                handler(newValue, oldValue) {
                    let allChecked = newValue.records.filter(o => o.checked
                    ).length == newValue.records.length
                    this.checkAll = allChecked && newValue.records.length > 0
                },
                deep: true
            }
        },
        methods: {
            doList(page) {
                page = page || this.data.page
                this.loading = true
                this.$api.post('member_message', {
                        page: page,
                        pageSize: this.data.pageSize,
                        search: JSON.stringify(this.search)
                    }, res => {
                        this.data.page = res.data.page
                        this.data.pageSize = res.data.pageSize
                        this.data.total = res.data.total
                        let records = res.data.records
                        records.map(o => o.checked = false
                        )
                        this.data.records = records
                        this.loading = false
                    },
                    res => {
                        this.loading = false
                    }
                )
            },
            doRead(ids) {
                this.$api.post('member_message/read', {ids: ids.join(',')}, res => {
                    if (ids.length > 1
                    ) {
                        this.doList(1)
                    }
                    else {
                        this.data.records.forEach(o => {
                            if (o.id == ids[0]
                            ) {
                                o.status = MemberMessageStatus.READ.value
                            }
                        })
                    }
                })
            },
            doDelete(ids) {
                this.$api.post('member_message/delete', {ids: ids.join(',')}, res => {
                    this.doList(1)
                })
            }
        },
    }
</script>
