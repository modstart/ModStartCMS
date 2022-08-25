<template>
    <div>
        <el-input-number :value="data" @change="onDataChange" :placeholder="placeholder"></el-input-number>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";


    export default {
        name: "NumberText",
        mixins: [FieldInputMixin],
    }
</script>
