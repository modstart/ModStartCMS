<template>
    <div>
        <DataSelector ref="fileDialog"
                      :url="fileDialogUrl"
                      category="file"
                      :max="1"
                      :child-key="childKey"
                      @on-select="doFileSelect"
        />
        <div class="pb-file-selector">
            <div class="item" v-if="datav">
                <div class="file-cover">{{fileName()}}</div>
                <div class="mask">
                    <a href="javascript:;" @click="doDelete()">
                        <i class="iconfont icon-trash"></i>
                    </a>
                </div>
            </div>
            <a class="plus" href="javascript:;" v-if="!datav" @click="doSelect">
                <i class="iconfont icon-plus"></i>
                {{selectText}}
            </a>
        </div>
    </div>
</template>

<style lang="less" scoped>
    .pb-file-selector {
        max-width: 200px;

        .plus {
            line-height: 28px;
            text-align: center;
            color: #999;
            display: block;
            background-color: #fff;
            border: 1px solid #c0ccda;
            font-size: 12px;
            border-radius: 3px;
        }

        .item {
            font-size: 12px;
            overflow: hidden;
            background-color: #fff;
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            border: 1px solid #c0ccda;
            border-radius: 3px;
            box-sizing: border-box;
            position: relative;
            width: 100%;
            height: 28px;
            display: inline-block;

            .file-cover {
                color: #999;
                padding: 0 5px;
            }

            &:hover {
                .mask {
                    display: block;
                }
            }

            .mask {
                background: rgba(0, 0, 0, 0.5);
                color: #FFF;
                text-align: center;
                display: none;
                position: absolute;
                top: 0px;
                right: 0px;
                bottom: 0px;
                left: 0px;

                a {
                    color: #CCC;
                    display: inline-block;
                    line-height: 28px;
                    padding: 0 5px;

                    &:hover {
                        color: #FFF;
                    }
                }
            }
        }
    }
</style>

<script>
    import DataSelector from "./DataSelector";

    export default {
        name: "FileSelector",
        components: {DataSelector},
        model: {
            prop: 'data',
            event: 'update'
        },
        props: {
            data: {
                type: String,
                default: ''
            },
            fileDialogUrl: {
                type: String,
                default: 'member_data/file_manager'
            },
            childKey: {
                type: String,
                default: '_child',
            },
            selectText: {
                type: String,
                default: '选择文件',
            },
            doSelectCustom: {
                type: Function,
                default: null,
            }
        },
        data() {
            return {
                datav: '',
                previewFile: '',
            }
        },
        mounted() {
            this.datav = this.data
        },
        watch: {
            data(newValue, oldValue) {
                if (newValue !== this.datav) {
                    this.datav = newValue
                }
            },
            datav(newValue, oldValue) {
                this.$emit('update', this.datav)
                this.$emit('change', this.datav)
            }
        },
        methods: {
            fileName() {
                return this.datav.substring(this.datav.lastIndexOf('/') + 1)
            },
            doFileSelect(files) {
                this.datav = files[0].path
            },
            doDelete() {
                this.datav = ''
            },
            doSelect() {
                if (null === this.doSelectCustom) {
                    this.$refs.fileDialog.show()
                } else {
                    this.doSelectCustom(path => {
                        this.datav = path
                        this.$emit('update', path)
                    })
                }
            },
        }
    }
</script>
