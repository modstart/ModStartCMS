<template>
    <div>
        <el-input :value="data" :placeholder="placeholder" @input="onDataChange"></el-input>
    </div>
</template>

<script>
    import {FieldInputMixin} from "../../lib/fields-config";
    export default {
        name: "TextInput",
        mixins: [FieldInputMixin],
    }
</script>
