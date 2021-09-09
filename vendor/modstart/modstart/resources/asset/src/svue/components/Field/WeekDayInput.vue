<template>
    <el-checkbox-group v-model="datav" placeholder="请选择">
        <el-checkbox :label="1">星期一</el-checkbox>
        <el-checkbox :label="2">星期二</el-checkbox>
        <el-checkbox :label="3">星期三</el-checkbox>
        <el-checkbox :label="4">星期四</el-checkbox>
        <el-checkbox :label="5">星期五</el-checkbox>
        <el-checkbox :label="6">星期六</el-checkbox>
        <el-checkbox :label="0">星期日</el-checkbox>
    </el-checkbox-group>
</template>

<script>
    import {SelectorIdsMixin} from "../Mixins/selector";

    export default {
        name: "WeekDayInput",
        mixins: [SelectorIdsMixin],
    }
</script>
