<template>
    <div class="pb-dialog-link-selector">
        <el-tabs>
            <el-tab-pane v-for="(linkGroup,linkGroupIndex) in links" :label="linkGroup.title"
                         :key="linkGroupIndex">
                <div class="pb-dialog-link-selector-item" v-for="(link,linkIndex) in linkGroup.list"
                     :key="linkIndex" @click="doSelect(link)">
                    <span class="title">{{link.title}}</span>
                    <span class="link">({{link.link}})</span>
                </div>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
    export default {
        name: "DialogLinkSelector",
        data() {
            return {
                links: window.__data.links
            }
        },
        methods: {
            doSelect(link) {
                // console.log('link', link)
                parent.__selectorDialogItems = [JSON.parse(JSON.stringify(link))]
                parent.__selectorDialog.close()
            }
        }
    }
</script>

<style lang="less" scoped>
    .pb-dialog-link-selector {
        position: relative;
    }

    .pb-dialog-link-selector-item {
        display: block;
        background: #FFF;
        margin: 0.2rem 0;
        line-height: 1.3rem;
        padding: 0 0.5rem;
        border-radius: 0.2rem;
        cursor: pointer;
        border: 1px solid #EEE;

        .link {
            color: #999;
            font-family: "Source Code Pro", monospace;
        }

        &:hover {
            border: 1px solid var(--color-warning);
        }
    }
</style>