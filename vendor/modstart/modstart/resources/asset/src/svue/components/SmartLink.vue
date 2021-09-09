<template>
    <a href="javascript:void(0);" @click="go">
        <slot></slot>
    </a>
</template>

<script>
    import {Routes} from './../../main/routes'
    import {Dialog} from "../lib/dialog";

    export default {
        props: {
            to: {
                type: String,
                default: undefined,
            },
            confirm: {
                type: String,
                default: null,
            },
            relative: {
                type: Boolean,
                default: false,
            },
            // 在slot时不能传递 @click 方法进来可以传递 :linkclick="()=>doFoo()"
            linkclick: {
                type: Function,
                default: null
            },
        },
        data() {
            return {}
        },
        computed: {
            prefix() {
                if (Routes) {
                    return Routes.prefix
                }
                return ''
            }
        },
        methods: {
            go() {
                var _go = () => {
                    if (this._events.click && this._events.click[0]) {
                        this._events.click[0]()
                    }
                    if (this.linkclick) {
                        this.linkclick()
                    }
                    if (undefined === this.to) {
                        return
                    }
                    if (this.to.indexOf('[url]') === 0) {
                        window.location.href = this.to.substring(5)
                    } else {
                        let target = this.prefix + '/' + this.to
                        if (this.relative) {
                            let pcs = this.$router.currentRoute.fullPath.split('/')
                            pcs.pop()
                            pcs.push(this.to)
                            target = pcs.join('/')
                        }
                        if (this.$router.currentRoute.fullPath !== target) {
                            this.$router.push(target)
                        }
                    }
                }
                if (this.confirm) {
                    Dialog.confirm(this.confirm, () => _go())
                } else {
                    _go()
                }
            }
        }
    }
</script>
