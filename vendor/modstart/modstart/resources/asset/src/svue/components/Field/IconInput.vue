<template>
    <div class="pb-icon-input">
        <div>
            <i class="pb-icon-input-preview" v-if="!!currentData" @click="dialogVisible=true" :class="currentData"></i>
            <el-input
                    style="width:15em;"
                    ref="input"
                    placeholder="直接输入或选择图标"
                    v-model="currentData">
            </el-input>
            <el-button v-if="inline" @click="dialogVisible=true">选择</el-button>
        </div>
        <div v-if="inline">
            <el-dialog :visible.sync="dialogVisible" append-to-body width="80%">
                <div slot="title">
                    选择图标
                </div>
                <div>
                    <div style="position:relative;">
                        <el-tabs>
                            <el-tab-pane v-for="(iconGroup,iconGroupIndex) in filterIcons" :label="iconGroup.title"
                                         :key="iconGroupIndex">
                                <div class="pb-icon-input-item-list">
                                    <a href="javascript:;" class="pb-icon-input-item"
                                       v-for="(icon,iconIndex) in iconGroup.list"
                                       :key="iconIndex"
                                       @click="currentData=icon.cls;dialogVisible=false;">
                                        <i :class="icon.cls"></i>
                                        <span class="title">{{icon.title}}</span>
                                    </a>
                                </div>
                            </el-tab-pane>
                        </el-tabs>
                        <div style="width:10em;position:absolute;top:5px;right:0px;">
                            <el-input
                                    placeholder="输入搜索"
                                    prefix-icon="el-icon-search"
                                    v-model="search">
                            </el-input>
                        </div>
                    </div>
                </div>
            </el-dialog>
        </div>
        <div v-else style="position:relative;">
            <el-tabs>
                <el-tab-pane v-for="(iconGroup,iconGroupIndex) in filterIcons" :label="iconGroup.title"
                             :key="iconGroupIndex">
                    <div class="pb-icon-input-item-list">
                        <a href="javascript:;" class="pb-icon-input-item" v-for="(icon,iconIndex) in iconGroup.list"
                           :key="iconIndex"
                           @click="currentData=icon.cls">
                            <i :class="icon.cls"></i>
                            <span class="title">{{icon.title}}</span>
                        </a>
                    </div>
                </el-tab-pane>
            </el-tabs>
            <div style="width:10em;position:absolute;top:5px;right:0px;">
                <el-input
                        placeholder="输入搜索"
                        prefix-icon="el-icon-search"
                        v-model="search">
                </el-input>
            </div>
        </div>
    </div>
</template>

<script>
    import {FieldInputMixin, FieldVModel} from "../../lib/fields-config";

    export default {
        name: "IconInput",
        mixins: [FieldInputMixin, FieldVModel],
        props: {
            icons: {
                type: Array,
                default: () => [],
            },
            inline: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                icons: [],
                search: '',
                dialogVisible: false,
            }
        },
        computed: {
            filterIcons() {
                let filter = []
                const searchs = this.search.split(' ').map(o => o.trim()).filter(o => !!o)
                this.icons.forEach(o => {
                    filter.push({
                        title: o.title,
                        list: o.list.filter(oo => {
                            if (!searchs.length) return true
                            for (let search of searchs) {
                                if (search && !oo.title.includes(search)) {
                                    return false
                                }
                            }
                            return true
                        })
                    })
                })
                return filter
            },
        },
    }
</script>

<style lang="less">
    .pb-icon-input {
        .pb-icon-input-preview {
            font-size: 1rem;
            height: 1.5rem;
            width: 1.5rem;
            line-height: 1.5rem;
            text-align: center;
            border-radius: 0.2rem;
            display: inline-block;
            vertical-align: middle;
            cursor: pointer;
            background: #EEE;
        }
    }

    .pb-icon-input-item-list {
        overflow: auto;
        max-height: 17.5rem;
        border: 1px solid #EEE;
        border-radius: 0.2rem;
        padding: 0.5rem;

        .pb-icon-input-item {
            display: block;
            float: left;
            width: 3rem;
            height: 3rem;
            color: #666;
            border: 1px solid transparent;
            text-align: center;
            border-radius: 0.2rem;
            padding: 0 0.2rem;
            white-space: nowrap;
            text-overflow: ellipsis;

            &:hover {
                color: var(--color-primary);
                border-color: #EEE;
            }

            i {
                display: block;
                font-size: 1rem;
                height: 2rem;
                line-height: 2rem;
            }

            .title {
                display: block;
                height: 1rem;
                line-height: 1rem;
                overflow: hidden;
                font-size: 0.5rem;
            }
        }
    }
</style>
