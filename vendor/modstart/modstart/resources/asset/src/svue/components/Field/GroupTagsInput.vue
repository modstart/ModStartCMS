<template>
    <div class="pb-group-tags-input">
        <div v-if="!option.length" class="ub-text-muted">
            暂无标签
        </div>
        <table v-else>
            <tr v-for="(groupTagItem,groupTagIndex) in option" :key="groupTagIndex">
                <td style="width:6em;vertical-align:top;">{{groupTagItem.groupTitle}}</td>
                <td>
                    <el-checkbox v-for="(groupTagItemItem,groupTagItemIndex) in groupTagItem.groupTags"
                                 :key="groupTagItemIndex"
                                 :value="currentData.includes(groupTagItemItem.id)"
                                 @change="checked=>onQuestionTagChange(checked,groupTagItemItem.id)"
                    >
                        {{groupTagItemItem.title}}
                    </el-checkbox>
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
import {FieldInputMixin, FieldVModel} from "../../lib/fields-config";

export default {
    name: "GroupTagsInput",
    mixins: [FieldInputMixin, FieldVModel],
    methods: {
        onQuestionTagChange(checked, id) {
            if (checked) {
                if (!this.currentData.includes(id)) {
                    this.currentData.push(id)
                }
            } else {
                if (this.currentData.includes(id)) {
                    this.currentData.splice(this.currentData.indexOf(id), 1)
                }
            }
            this.currentData = JSON.parse(JSON.stringify(this.currentData))
        }
    }
}
</script>

<style lang="less">
.pb-group-tags-input {
    table tr td label.el-checkbox {
        display: inline-block;
        min-width: 6em;
    }
}
</style>
