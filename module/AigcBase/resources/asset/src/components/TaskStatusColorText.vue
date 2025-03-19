<script>
import {JobStatus} from "../lib/constant";

export default {
    name: "TaskStatusColorText",
    props: {
        status: ''
    },
    data() {
        return {
            JobStatus
        }
    }
}
</script>

<template>
    <div>
        <div v-if="status===JobStatus.QUEUE.value" class="ub-text-warning">
            <i class="iconfont icon-dot-sm"></i>
            处理中
        </div>
        <div v-if="status===JobStatus.PROCESS.value" class="ub-text-warning">
            <i class="iconfont icon-dot-sm"></i>
            处理中
        </div>
        <div v-if="status===JobStatus.SUCCESS.value" class="ub-text-success">
            <i class="iconfont icon-dot-sm"></i>
            已完成
        </div>
        <div v-if="status===JobStatus.FAIL.value" class="ub-text-danger">
            <i class="iconfont icon-dot-sm"></i>
            失败
        </div>
    </div>
</template>

<style scoped>

</style>
