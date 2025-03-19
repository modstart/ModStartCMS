<script>
import {VModelMixin} from '@ModStartAsset/svue/components/Mixins/selector'

export default {
    name: "TaskStatusSelector",
    mixins: [VModelMixin],
}
</script>

<template>
    <div class="tw-bg-gray-100 tw-rounded-lg tw-p-1">
        <a href="javascript:;" class="btn"
           :class="{'btn-primary':!modelValue}"
           @click="modelValue=null"
        >全部</a>
        <a href="javascript:;" class="btn"
           :class="{'btn-primary':modelValue === 'success'}"
           @click="modelValue='success'"
        >已完成</a>
        <a href="javascript:;" class="btn"
           :class="{'btn-primary':modelValue === 'processing'}"
           @click="modelValue='processing'"
        >处理中</a>
        <a href="javascript:;" class="btn"
           :class="{'btn-primary':modelValue === 'failed'}"
           @click="modelValue='failed'"
        >失败</a>
    </div>
</template>

