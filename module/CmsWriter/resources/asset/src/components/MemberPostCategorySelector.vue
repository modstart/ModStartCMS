<template>
    <div>
        <div v-if="loading" class="ub-text-muted">
            正在加载中...
        </div>
        <div v-if="!loading && !records.length" class="ub-text-muted">
            暂无分类，请先 <a :href="$url.web('writer/category')">添加分类</a>
        </div>
        <div v-if="records.length>0">
            <el-select v-if="records.length>0" v-model="currentData" style="width:100%;">
                <el-option v-for="item in records"
                           :key="item.id"
                           :label="item.title"
                           :value="item.id"></el-option>
            </el-select>
        </div>
    </div>
</template>

<script>
    import {FieldVModel} from "@ModStartAsset/svue/lib/fields-config"

    export default {
        name: "MemberPostCategorySelector",
        mixins: [FieldVModel],
        data() {
            return {
                loading: true,
                records: [],
            }
        },
        mounted() {
            this.load()
        },
        methods: {
            load() {
                this.$api.post(this.$url.api('writer/category/all'), {}, res => {
                    this.records = res.data
                    this.loading = false
                    if (!this.currentData) {
                        for (let record of this.records) {
                            this.currentData = record.id
                            return
                        }
                    }
                })
            }
        }
    }
</script>
