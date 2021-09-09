<template>
    <el-switch v-model="datav"></el-switch>
</template>

<script>
    import {SelectorBooleanMixin} from "../Mixins/selector";

    export default {
        name: "SwitchInput",
        mixins: [SelectorBooleanMixin],
    }
</script>
