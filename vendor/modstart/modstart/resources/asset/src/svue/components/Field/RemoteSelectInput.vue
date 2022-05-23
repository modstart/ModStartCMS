<template>
    <div v-loading="loading">
        <el-select v-model="currentData" :placeholder="placeholder">
            <el-option :value="0" :label="placeholder"></el-option>
            <el-option
                v-for="item in option"
                :key="item.id"
                :label="item.title"
                :value="item.id">
            </el-option>
        </el-select>
    </div>
</template>

<script>
import {FieldVModel} from "@ModStartAsset/svue/lib/fields-config"

export default {
    name: "RemoteSelectInput",
    mixins: [FieldVModel],
    props: {
        url: {
            type: String,
            default: null,
        },
        placeholder: {
            type: String,
            default: '请选择',
        }
    },
    data() {
        return {
            loading: true,
            option: [],
        }
    },
    mounted() {
        if (this.url) {
            this.$api.post(this.url, {}, res => {
                this.loading = false
                this.option = res.data
            })
        }
    },
    methods: {
        getOptions() {
            return this.option
        }
    }
}
</script>
