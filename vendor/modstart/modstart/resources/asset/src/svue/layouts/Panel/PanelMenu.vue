<template>
    <div>
        <el-menu v-if="(isPC && !collapse) || collapse" :default-active="active" @open="handleOpen" @close="handleClose"
                 :default-openeds="defaultOpeneds"
                 :collapse="isPC && collapse">
            <template v-for="(menu0Item,menu0Index) in $store.state.panel.menus"
                      v-if="menu0Item.show">
                <el-submenu v-if="('children' in menu0Item) && menu0Item.children.length>0"
                            :key="menu0Index+''"
                            :index="menu0Index+''">
                    <template slot="title">
                        <i :class="menu0Item.iconClass"></i>
                        <span>{{menu0Item.title}}</span>
                    </template>
                    <template v-for="(menu1Item,menu1Index) in menu0Item.children"
                              v-if="menu1Item.show">
                        <el-submenu v-if="('children' in menu1Item) && menu1Item.children.length>0"
                                    :key="menu0Index+'-'+menu1Index"
                                    :index="menu0Index+'-'+menu1Index">
                            <template slot="title">
                                <span>{{menu1Item.title}}</span>
                            </template>
                            <el-menu-item v-for="(menu2Item,menu2Index) in menu1Item.children"
                                          v-if="menu2Item.show"
                                          @click="doMenuClick(menu0Index+'-'+menu1Index+'-'+menu2Index)"
                                          :key="menu0Index+'-'+menu1Index+'-'+menu2Index"
                                          :index="menu0Index+'-'+menu1Index+'-'+menu2Index">
                                {{menu2Item.title}}
                            </el-menu-item>
                        </el-submenu>
                        <el-menu-item v-else
                                      @click="doMenuClick(menu0Index+'-'+menu1Index)"
                                      :key="menu0Index+'-'+menu1Index"
                                      :index="menu0Index+'-'+menu1Index">
                            {{menu1Item.title}}
                        </el-menu-item>
                    </template>
                </el-submenu>
                <el-menu-item v-else
                              @click="doMenuClick(menu0Index+'')"
                              :key="menu0Index+''"
                              :index="menu0Index+''">
                    <i :class="menu0Item.iconClass"></i>
                    <span slot="title">{{menu0Item.title}}</span>
                </el-menu-item>
            </template>
        </el-menu>
    </div>
</template>

<script>
    import {Device} from "../../lib/device";

    export default {
        props: ['collapse'],
        data() {
            return {
                active: '',
            }
        },
        computed: {
            isPC() {
                return Device.isPC()
            },
            defaultOpeneds() {
                if (!this.$store.state.panel.style.alwaysShowMenu) {
                    return []
                }
                return this.$store.state.panel.menus.map((o, i) => i + '')
            }
        },
        methods: {
            doMenuClick(index) {
                this.active = index
                const pcs = index.split('-')
                let menuItem
                switch (pcs.length) {
                    case 1:
                        menuItem = this.$store.state.panel.menus[parseInt(pcs[0])]
                        break;
                    case 2:
                        menuItem = this.$store.state.panel.menus[parseInt(pcs[0])].children[parseInt(pcs[1])]
                        break;
                    case 3:
                        menuItem = this.$store.state.panel.menus[parseInt(pcs[0])].children[parseInt(pcs[1])].children[parseInt(pcs[2])]
                        break;
                }
                if (!this.isPC) {
                    this.$emit('onPanelMenuHide')
                }
                if (menuItem.url.indexOf('[url]') === 0) {
                    let url = menuItem.url.substring(5)
                    if (url.startsWith('[blank]')) {
                        url = url.substring(7)
                        window.open(url)
                        return
                    }
                    window.location.href = url
                } else {
                    console.log('jump', this.$route.path, menuItem.url)
                    if (this.$route.path !== menuItem.url) {
                        this.$router.push(menuItem.url)
                    }
                }
            },
            handleOpen(key, keyPath) {
                // console.log(key, keyPath);
            },
            handleClose(key, keyPath) {
                // console.log(key, keyPath);
            }
        }
    }
</script>

