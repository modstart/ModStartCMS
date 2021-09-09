<template>
    <div class="pb-color-selector">
        <div class="item blank"
             :class="{active:datav===''}"
             @click="doSelect('')"></div>
        <span class="item" :class="{active:datav===item}"
              @click="doSelect(item)"
              :style="{backgroundColor:item}"
              v-for="(item,itemIndex) in list"></span>
    </div>
</template>

<script>
    export default {
        name: "ColorSelector",
        model: {
            prop: 'data',
            event: 'update'
        },
        props: {
            data: {
                type: String,
                default: ''
            },
            list: {
                type: Array,
                default: () => {
                    return [
                        '#61A5E9',
                        '#6DB6CF',
                        '#84C06D',
                        '#F3D668',
                        '#EAB25D',
                        '#E19075',
                        '#DE78A4',
                        '#C16EF0',
                        '#B39B91',
                        '#A6AEC4',
                        '#B6B4B7',
                        '#B0C3C0',
                        '#F2FD84',
                        '#6637EC',
                        '#B7F9DE',
                        '#5170EF',
                        '#ABCC6F',
                        '#DD726B',
                        '#E0D3A7',
                    ]
                }
            }
        },
        data() {
            return {
                datav: '',
            }
        },
        watch: {
            data: {
                handler(newValue, oldValue) {
                    if (newValue !== this.datav) {
                        this.datav = newValue
                    }
                },
                immediate: true
            }
        },
        methods: {
            doSelect(item) {
                this.datav = item
                this.$emit('update', item)
            }
        }
    }
</script>

<style lang="less" scoped>
    .pb-color-selector {
        .item {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-block;
            margin: 0 5px 5px 0;
            border: 5px solid #FFF;
            box-sizing: border-box;
            cursor: pointer;
            &.active {
                border: 5px solid #EEE;
                box-shadow: 0 0 3px #999;
            }
        }
        .blank {
            position: relative;
            transform: rotateZ(45deg);
            &:after {
                content: '';
                width: 20px;
                height: 1px;
                position: absolute;
                top: 10px;
                left: 0px;
                background: #999;
            }
            &:before {
                content: '';
                width: 20px;
                height: 20px;
                position: absolute;
                top: 0px;
                left: 0px;
                border: 1px solid #999;
                border-radius: 50%;
            }
        }
    }
</style>