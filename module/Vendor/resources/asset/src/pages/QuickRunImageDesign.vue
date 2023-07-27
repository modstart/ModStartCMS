<template>
    <div class="row">
        <div class="col-md-4">
            <div class="ub-content-box" style="position:sticky;top:0;">
                <div class="margin-bottom tw-flex">
                    <div class="tw-flex-grow">
                        <el-switch v-model="debug"
                                   active-text="打开调试"
                                   inactive-text="关闭调试"></el-switch>
                    </div>
                    <div>
                        <a href="javascript:;" class="btn btn-primary btn-sm" @click="doSave">
                            保存
                        </a>
                    </div>
                </div>
                <div id="pbDesignPreview"
                     :style="{height:height+'px'}"
                >
                    <div class="container"
                         :class="{debug:debug}"
                         :style="{width:data.width+'px',height:data.height+'px',transform:`scale(${scaleRatio})`}">
                        <div class="background"
                             :style="{backgroundImage:'url('+data.backgroundImage+')',width:data.width+'px',height:data.height+'px'}"
                        ></div>
                        <div class="blocks">
                            <template v-for="(b,bIndex) in data.blocks">
                                <div v-if="b.type==='text'"
                                     :data-index="bIndex"
                                     :style="{left:b.x+'px',top:b.y+'px',fontSize:b.data.size+'px',fontWeight:b.data.bold?'bold':'normal',color:b.data.color}"
                                     class="block-item">
                                    <div class="block-item-body"
                                         :style="{margin:textAlignMarginMap[b.data.align],textAlign:b.data.align}"
                                         v-html="textToHtml(b.data.text)"
                                    >
                                    </div>
                                </div>
                                <div v-else-if="b.type==='image'"
                                     :data-index="bIndex"
                                     :style="{left:b.x+'px',top:b.y+'px',width:b.data.width+'px',height:b.data.height+'px'}"
                                     class="block-item">
                                    <div class="block-item-body"
                                         :style="{width:b.data.width+'px',height:b.data.height+'px'}">
                                        <img :src="b.data.image"
                                             :style="{width:b.data.width+'px',height:b.data.height+'px',opacity:b.data.opacity/100}"
                                             class="image" draggable="false"/>
                                    </div>
                                </div>
                                <div v-else-if="b.type==='qrcode'"
                                     :data-index="bIndex"
                                     :style="{left:b.x+'px',top:b.y+'px',width:b.data.width+'px',height:b.data.height+'px'}"
                                     class="block-item">
                                    <div class="block-item-body"
                                         :style="{width:b.data.width+'px',height:b.data.height+'px'}">
                                        <img :src="$url.cdn('asset/image/qrcode-demo.png')"
                                             :style="{width:b.data.width+'px',height:b.data.height+'px'}"
                                             class="image" draggable="false"/>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="ub-content-box">
                <div class="ub-form vertical">
                    <div class="line">
                        <div class="label">尺寸</div>
                        <div class="field">
                            <el-input-number v-model="data.width"></el-input-number>
                            x
                            <el-input-number v-model="data.height"></el-input-number>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">背景图</div>
                        <div class="field">
                            <image-selector v-model="data.backgroundImage"
                                            gallery-enable
                                            upload-enable
                                            @update="onBackgroundUpdate"
                                            :image-dialog-url="$url.admin('data/file_manager')"
                            ></image-selector>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">字体</div>
                        <div class="field">
                            <file-selector v-model="data.font"
                                           gallery-enable
                                           upload-enable
                                           :file-dialog-url="$url.admin('data/file_manager')"
                            ></file-selector>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">内容</div>
                        <div class="field">
                            <table class="ub-table border head-dark">
                                <thead>
                                <tr>
                                    <th width="80">类型</th>
                                    <th width="120">位置</th>
                                    <th>内容</th>
                                    <th width="80">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(b,bIndex) in data.blocks">
                                    <td>
                                        <span class="ub-tag" v-if="b.type==='text'">文字</span>
                                        <span class="ub-tag" v-else-if="b.type==='image'">图片</span>
                                        <span class="ub-tag" v-else-if="b.type==='qrcode'">二维码</span>
                                    </td>
                                    <td>
                                        <el-input v-model="b.x">
                                            <template slot="prepend">X</template>
                                        </el-input>
                                        <el-input v-model="b.y">
                                            <template slot="prepend">Y</template>
                                        </el-input>
                                    </td>
                                    <td class="tw-leading-8">
                                        <div v-if="b.type==='text'">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="pb-block-field">
                                                        <div class="title">内容</div>
                                                        <div class="content">
                                                            <el-input v-model="b.data.text">
                                                                <template slot="append">
                                                                    <a href="javascript:;"
                                                                       @click="doSelectTextVariable(bIndex)">
                                                                        变量
                                                                    </a>
                                                                </template>
                                                            </el-input>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="pb-block-field">
                                                        <div class="title">字号</div>
                                                        <div class="content">
                                                            <el-input v-model="b.data.size"></el-input>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="pb-block-field">
                                                        <div class="title">粗体</div>
                                                        <div class="content tw-py-1">
                                                            <el-switch v-model="b.data.bold"></el-switch>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="pb-block-field">
                                                        <div class="title">颜色</div>
                                                        <div class="content">
                                                            <el-color-picker style="display:block;"
                                                                             v-model="b.data.color"></el-color-picker>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="pb-block-field">
                                                        <div class="title">对齐</div>
                                                        <div class="content">
                                                            <el-radio-group v-model="b.data.align">
                                                                <el-radio-button label="left">左</el-radio-button>
                                                                <el-radio-button label="center">中</el-radio-button>
                                                                <el-radio-button label="right">右</el-radio-button>
                                                            </el-radio-group>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="b.type==='image'">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="pb-block-field">
                                                        <div class="title">图片</div>
                                                        <div class="content tw-pt-1">
                                                            <image-selector v-model="b.data.image"
                                                                            gallery-enable
                                                                            upload-enable
                                                                            @update="onImageUpdate(bIndex,$event)"
                                                                            :image-dialog-url="$url.admin('data/file_manager')"
                                                            ></image-selector>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="pb-block-field">
                                                        <div class="title">宽度</div>
                                                        <div class="content">
                                                            <el-input v-model="b.data.width"></el-input>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="pb-block-field">
                                                        <div class="title">高度</div>
                                                        <div class="content">
                                                            <el-input v-model="b.data.height"></el-input>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="pb-block-field">
                                                        <div class="title">透明度</div>
                                                        <div class="content">
                                                            <el-slider v-model="b.data.opacity" :min="0" :max="100"
                                                                       show-input></el-slider>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="b.type==='qrcode'">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="pb-block-field">
                                                        <div class="title">内容</div>
                                                        <div class="content">
                                                            <el-input v-model="b.data.text">
                                                                <template slot="append">
                                                                    <a href="javascript:;"
                                                                       @click="doSelectTextVariable(bIndex)">
                                                                        变量
                                                                    </a>
                                                                </template>
                                                            </el-input>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="pb-block-field">
                                                        <div class="title">宽度</div>
                                                        <div class="content">
                                                            <el-input v-model="b.data.width"></el-input>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="pb-block-field">
                                                        <div class="title">高度</div>
                                                        <div class="content">
                                                            <el-input v-model="b.data.height"></el-input>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="javascript:;" class="ub-text-muted"
                                           @click="doUp(data.blocks,bIndex)">
                                            <i class="iconfont icon-direction-up"></i>
                                        </a>
                                        <a href="javascript:;" class="ub-text-muted"
                                           @click="doDown(data.blocks,bIndex)">
                                            <i class="iconfont icon-direction-down"></i>
                                        </a>
                                        <a href="javascript:;" class="ub-text-muted"
                                           @click="data.blocks.splice(bIndex,1)">
                                            <i class="iconfont icon-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <a href="javascript:;" class="btn btn-sm btn-round"
                                           @click="doBlockItemAdd('text')">
                                            <i class="iconfont icon-plus"></i>
                                            文字
                                        </a>
                                        <a href="javascript:;" class="btn btn-sm btn-round"
                                           @click="doBlockItemAdd('image')">
                                            <i class="iconfont icon-plus"></i>
                                            图片
                                        </a>
                                        <a href="javascript:;" class="btn btn-sm btn-round"
                                           @click="doBlockItemAdd('qrcode')">
                                            <i class="iconfont icon-plus"></i>
                                            二维码
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <el-dialog :visible.sync="dialogTextVariableVisible" append-to-body>
            <div slot="title">
                选择变量
            </div>
            <div>
                <table class="ub-table border hover mini">
                    <thead>
                    <tr>
                        <th>变量</th>
                        <th>说明</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="tw-cursor-pointer"
                        v-for="(v,vIndex) in variables"
                        :key="vIndex"
                        @click="doDialogTextVariableSelect('${' + v.value + '}')">
                        <td>
                            <span class="tw-font-mono">
                                {{ '${' + v.value + '}' }}
                            </span>
                        </td>
                        <td>
                            {{ v.value }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </el-dialog>
    </div>
</template>

<script>
export default {
    name: "QuickRunImageDesign",
    data() {
        return {
            textAlignMarginMap: {
                left: 0,
                center: '0 50% 0 -50% ',
                right: '0 100% 0 -100%',
            },
            width: 100,
            debug: true,
            dialogTextVariableIndex: 0,
            dialogTextVariableVisible: false,
            variables: window._data.variables,
            data: window._data.imageConfig,
        }
    },
    computed: {
        height() {
            if (this.data.width > 0 && this.data.height > 0) {
                return this.width * this.data.height / this.data.width
            }
            return this.width * 1.5
        },
        scaleRatio() {
            return this.width / this.data.width
        },
    },
    mounted() {
        const $preview = $('#pbDesignPreview');
        MS.ui.onResize($preview[0], () => {
            this.width = $preview.width()
        })
        const me = this;
        let dragging = {
            $item: null,
            index: 0,
            startX: 0,
            startY: 0,
            startLeft: 0,
            startTop: 0,
        }
        $preview.on('mousedown', '.block-item', function (e) {
            const $this = $(this)
            dragging.$item = $this
            dragging.index = parseInt($this.data('index'))
            dragging.startX = e.clientX
            dragging.startY = e.clientY
            dragging.startLeft = parseInt($this.css('left'))
            dragging.startTop = parseInt($this.css('top'))
        });
        $preview.on('mousemove', function (e) {
            if (!dragging.$item) {
                return
            }
            const ratio = me.scaleRatio;
            const left = parseInt(dragging.startLeft + (e.clientX - dragging.startX) / ratio);
            const top = parseInt(dragging.startTop + (e.clientY - dragging.startY) / ratio);
            dragging.$item.css({
                left: left + 'px',
                top: top + 'px',
            });
            me.data.blocks[dragging.index].x = left;
            me.data.blocks[dragging.index].y = top;
        });
        $(document).on('mouseup', function (e) {
            dragging.$item = null;
        });
    },
    methods: {
        textToHtml(value) {
            value = value.replace(/\[BR\]/g, '<br/>')
            return value
        },
        doUp: MS.collection.sort.up,
        doDown: MS.collection.sort.down,
        onBackgroundUpdate() {
            MS.image.getSize(this.data.backgroundImage, param => {
                this.data.width = param.width
                this.data.height = param.height
            })
        },
        onImageUpdate(bIndex, imagePath) {
            MS.image.getSize(imagePath, param => {
                this.data.blocks[bIndex].data.width = param.width
                this.data.blocks[bIndex].data.height = param.height
            });
        },
        doSelectTextVariable(index) {
            this.dialogTextVariableVisible = true
            this.dialogTextVariableIndex = index
        },
        doDialogTextVariableSelect(text) {
            this.data.blocks[this.dialogTextVariableIndex].data.text = text
            this.dialogTextVariableVisible = false
        },
        doBlockItemAdd(type) {
            let block = {
                type: type,
                x: parseInt(this.width / 2),
                y: parseInt(this.height / 2),
                data: {},
            }
            switch (type) {
                case 'text':
                    block.data.text = '测试文字'
                    block.data.color = '#000000'
                    block.data.bold = false
                    block.data.size = 20
                    block.data.align = 'left'
                    break
                case 'image':
                    block.data.image = this.$url.web('placeholder/100x100')
                    block.data.width = 100
                    block.data.height = 100
                    break
                case 'qrcode':
                    block.data.text = '二维码内容'
                    block.data.width = 100
                    block.data.height = 100
                    break
            }
            this.data.blocks.push(block)
        },
        doSave() {
            this.$dialog.loadingOn()
            this.$api.post(window.location.href, {data: JSON.stringify(this.data)}, res => {
                this.$dialog.loadingOff()
                MS.api.defaultCallback(res)
            }, res => {
                this.$dialog.loadingOff()
            })
        }
    }
}
</script>

<style lang="less" scoped>
#pbDesignPreview {
    outline: 1px solid #EEE;
    width: 100%;
    overflow: hidden;
    user-select: none;

    .container {
        transform-origin: 0 0;
        position: relative;

        .background {
            background-repeat: no-repeat;
        }

        &.debug .blocks .block-item .block-item-body {
            outline: 1px solid red;
        }

        .blocks {
            .block-item {
                position: absolute;
                cursor: move;

                .block-item-body {
                    white-space: nowrap;
                }
            }
        }
    }
}

.pb-block-field {
    display: flex;
    border-radius: 0.25rem;
    padding: 0.25rem;
    justify-content: center;
    align-items: center;
    margin-bottom: 0.25rem;
    background: #F2F5F9;

    .title {
        margin-right: 5px;
    }

    .content {
        flex: 1;
        width: 0;
    }
}

</style>
