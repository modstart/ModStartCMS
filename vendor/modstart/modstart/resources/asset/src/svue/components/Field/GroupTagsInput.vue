<template>
    <div class="pb-group-tags-input">
        <div v-if="!option.length" class="ub-text-muted">
            无
        </div>
        <div v-else>
            <table>
                <tr v-for="(gt,gtIndex) in option"
                    v-if="groupFilter(gt)"
                    :key="gtIndex">
                    <td style="width:6em;vertical-align:top;">{{ gt[groupTitleKey] }}</td>
                    <td>
                        <el-checkbox v-for="(gtItem,gtIndex) in gt[childKey]"
                                     :key="gtIndex"
                                     :value="currentData.includes(gtItem.id)"
                                     @change="checked=>onChange(checked,gtItem.id)"
                        >
                            {{ gtItem.title }}
                        </el-checkbox>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
import {FieldInputMixin, FieldVModel} from "../../lib/fields-config";

export default {
    name: "GroupTagsInput",
    mixins: [FieldInputMixin, FieldVModel],
    props: {
        groupTitleKey: {
            type: String,
            default: 'groupTitle'
        },
        childKey: {
            type: String,
            default: 'groupTags'
        },
        groupFilter: {
            type: Function,
            default: (group) => true
        }
    },
    methods: {
        onChange(checked, id) {
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
