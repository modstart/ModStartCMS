<template>
    <div>
        <el-input-number :value="data" @change="onDataChange" :precision="2" :step="0.1" :placeholder="placeholder"></el-input-number>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";


    export default {
        name: "DecimalInput",
        mixins: [FieldInputMixin],
    }
</script>
