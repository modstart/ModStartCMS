<style lang="less">
    .pb-rich-editor {
        border:1px solid #EEE;
        background: #FFF;
        position: relative;
        border-radius: 3px;
        &__content {
            margin:10px;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
            * {
                caret-color: currentColor;
            }
            pre {
                padding: 0.7rem 1rem;
                border-radius: 5px;
                background: #FFF;
                color: #333;
                font-size: 0.8rem;
                overflow-x: auto;
                code {
                    display: block;
                }
            }
            p code {
                display: inline-block;
                padding: 0 0.4rem;
                border-radius: 5px;
                font-size: 0.8rem;
                font-weight: bold;
                background: rgba(#333, 0.1);
                color: rgba(#333, 0.8);
            }
            ul,
            ol {
                padding-left: 1rem;
            }
            li > p,
            li > ol,
            li > ul {
                margin: 0;
            }
            a {
                color: inherit;
                text-decoration:underline;
                color:#1191ff;
            }
            blockquote {
                border-left: 3px solid rgba(#333, 0.1);
                color: rgba(#333, 0.8);
                padding-left: 0.8rem;
                font-style: italic;

                p {
                    margin: 0;
                }
            }
            img {
                max-width: 100%;
                border-radius: 3px;
            }
            table {
                border-collapse: collapse;
                table-layout: fixed;
                width: 100%;
                margin: 0;
                overflow: hidden;
                td, th {
                    min-width: 1em;
                    border: 2px solid #CCC;
                    padding: 3px 5px;
                    vertical-align: top;
                    box-sizing: border-box;
                    position: relative;
                    > * {
                        margin-bottom: 0;
                    }
                }
                th {
                    font-weight: bold;
                    text-align: left;
                }
                .selectedCell:after {
                    z-index: 2;
                    position: absolute;
                    content: "";
                    left: 0; right: 0; top: 0; bottom: 0;
                    background: rgba(200, 200, 255, 0.4);
                    pointer-events: none;
                }
                .column-resize-handle {
                    position: absolute;
                    right: -2px; top: 0; bottom: 0;
                    width: 4px;
                    z-index: 20;
                    background-color: #adf;
                    pointer-events: none;
                }
            }
            .tableWrapper {
                margin: 1em 0;
                overflow-x: auto;
            }
            .resize-cursor {
                cursor: ew-resize;
                cursor: col-resize;
            }
            .has-focus {
                border-radius: 3px;
                box-shadow: 0 0 0 3px #3ea4ffe6;
            }
        }
        .menubar {
            border-bottom:1px solid #EEE;
            border-radius: 3px;
            padding:5px;
            button {
                border: none;
                background: transparent;
                border-radius: 3px;
                display: inline-block;
                line-height: 30px;
                padding: 0 10px;
                cursor: pointer;
                margin-right: 5px;
                color: #333;
                min-width:30px;
                text-align: center;
                &:hover {
                    background: #EEE;
                }
                &.is-active {
                    background: #CCC;
                }
            }
        }
        .menububble {
            position: absolute;
            display: flex;
            z-index: 20;
            background: #333;
            border-radius: 5px;
            padding: 0.3rem;
            margin-bottom: 0.5rem;
            transform: translateX(-50%);
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.2s, visibility 0.2s;

            &.is-active {
                opacity: 1;
                visibility: visible;
            }

            &__button {
                display: inline-flex;
                background: transparent;
                border: 0;
                color: #FFF;
                padding: 0.2rem 0.5rem;
                margin-right: 0.2rem;
                border-radius: 3px;
                cursor: pointer;

                &:last-child {
                    margin-right: 0;
                }

                &:hover {
                    background-color: rgba(#FFF, 0.1);
                }

                &.is-active {
                    background-color: rgba(#FFF, 0.2);
                }
            }

            &__form {
                display: flex;
                align-items: center;
            }

            &__input {
                font: inherit;
                border: none;
                background: transparent;
                color: #FFF;
            }
        }
    }
</style>

<template>
    <div class="pb-rich-editor">
        <editor-menu-bar :editor="editor" v-slot="{ commands, isActive }">
            <div class="menubar">
                <button class="menubar__button" :class="{ 'is-active': isActive.bold() }" @click="commands.bold">
                    <i class="iconfont icon-bold"></i>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.italic() }" @click="commands.italic">
                    <i class="iconfont icon-italic"></i>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.strike() }" @click="commands.strike">
                    <i class="iconfont icon-strike"></i>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.underline() }" @click="commands.underline">
                    <i class="iconfont icon-underline"></i>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.paragraph() }" @click="commands.paragraph">
                    <i class="iconfont icon-paragraph"></i>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 1 }) }" @click="commands.heading({ level: 1 })">
                    H1
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 2 }) }" @click="commands.heading({ level: 2 })">
                    H2
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.heading({ level: 3 }) }" @click="commands.heading({ level: 3 })">
                    H3
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.bullet_list() }" @click="commands.bullet_list">
                    <i class="iconfont icon-ul"></i>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.ordered_list() }" @click="commands.ordered_list">
                    <i class="iconfont icon-ol"></i>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.code() }" @click="commands.code">
                    <i class="iconfont icon-code"></i>
                </button>
                <button class="menubar__button" :class="{ 'is-active': isActive.code_block() }" @click="commands.code_block">
                    <i class="iconfont icon-code-alt"></i>
                </button>
                <button class="menubar__button" @click="commands.horizontal_rule">
                    <i class="iconfont icon-hr"></i>
                </button>
                <button class="menubar__button" @click="showImagePrompt(commands.image)">
                    <i class="iconfont icon-images"></i>
                </button>
                <button class="menubar__button" @click="showIframePrompt(commands.iframe)">
                    <i class="iconfont icon-images"></i>
                </button>

                <button class="menubar__button" @click="commands.createTable({rowsCount: 3, colsCount: 3, withHeaderRow: false })">
                    <i class="iconfont icon-table"></i>
                </button>

                <span v-if="isActive.table()">
                    <button class="menubar__button" @click="commands.deleteTable">
                        <i class="iconfont icon-delete-table"></i>
                    </button>
                    <button class="menubar__button" @click="commands.addColumnBefore">
                        <i class="iconfont icon-add-col-before"></i>
                    </button>
                    <button class="menubar__button" @click="commands.addColumnAfter">
                        <i class="iconfont icon-add-col-after"></i>
                    </button>
                    <button class="menubar__button" @click="commands.deleteColumn">
                        <i class="iconfont icon-delete-col"></i>
                    </button>
                    <button class="menubar__button" @click="commands.addRowBefore">
                        <i class="iconfont icon-add-row-before"></i>
                    </button>
                    <button class="menubar__button" @click="commands.addRowAfter">
                        <i class="iconfont icon-add-row-after"></i>
                    </button>
                    <button class="menubar__button" @click="commands.deleteRow" >
                        <i class="iconfont icon-delete-row"></i>
                    </button>
                    <button class="menubar__button" @click="commands.toggleCellMerge">
                        <i class="iconfont icon-combine-cells"></i>
                    </button>
                </span>

                <button class="menubar__button" @click="commands.undo">
                    <i class="iconfont icon-undo"></i>
                </button>
                <button class="menubar__button" @click="commands.redo">
                    <i class="iconfont icon-redo"></i>
                </button>

            </div>
        </editor-menu-bar>
        <editor-menu-bubble class="menububble" :editor="editor" @hide="hideLinkMenu" v-slot="{ commands, isActive, getMarkAttrs, menu }">
            <div class="menububble" :class="{ 'is-active': menu.isActive }" :style="`left: ${menu.left}px; bottom: ${menu.bottom}px;`">
                <form class="menububble__form" v-if="linkMenuIsActive" @submit.prevent="setLinkUrl(commands.link, linkUrl)">
                    <input class="menububble__input" type="text" v-model="linkUrl" placeholder="https://" ref="linkInput" @keydown.esc="hideLinkMenu"/>
                    <button class="menububble__button" @click="setLinkUrl(commands.link, null)" type="button">
                        <i class="iconfont icon-close-o"></i>
                    </button>
                </form>
                <template v-else>
                    <button class="menububble__button" @click="showLinkMenu(getMarkAttrs('link'))" :class="{ 'is-active': isActive.link() }">
                        <span>{{ isActive.link() ? '修改链接' : '增加链接'}}</span>
                        <i class="iconfont icon-link-alt"></i>
                    </button>
                </template>
            </div>
        </editor-menu-bubble>
        <editor-content class="pb-rich-editor__content" :editor="editor"/>
    </div>
</template>

<script>
    import {Editor, EditorContent, EditorMenuBar,EditorMenuBubble} from 'tiptap'
    import {
        Blockquote,
        CodeBlock,
        HardBreak,
        Heading,
        HorizontalRule,
        OrderedList,
        BulletList,
        ListItem,
        TodoItem,
        TodoList,
        Bold,
        Code,
        Italic,
        Link,
        Strike,
        Underline,
        History,
        Image,
        Table,
        TableHeader,
        TableCell,
        TableRow,
        Focus,
    } from 'tiptap-extensions'
    import {Iframe} from './RichEditor/Iframe'

    export default {
        components: {
            EditorContent,
            EditorMenuBar,
            EditorMenuBubble,
        },
        data() {
            return {
                editor: new Editor({
                    extensions: [
                        new Blockquote(),
                        new BulletList(),
                        new CodeBlock(),
                        new HardBreak(),
                        new Heading({levels: [1, 2, 3]}),
                        new HorizontalRule(),
                        new ListItem(),
                        new OrderedList(),
                        new TodoItem(),
                        new TodoList(),
                        new Link(),
                        new Bold(),
                        new Code(),
                        new Italic(),
                        new Strike(),
                        new Underline(),
                        new History(),
                        new Image(),
                        new Table({
                            resizable: true,
                        }),
                        new TableHeader(),
                        new TableCell(),
                        new TableRow(),
                        new Focus({
                            className: 'has-focus',
                            nested: true,
                        }),
                        new Iframe(),
                    ],
                    autoFocus: true,
                    content: ``,
                }),
                linkUrl: '',
                linkMenuIsActive:false,
            }
        },
        mounted() {

        },
        beforeDestroy() {
            this.editor.destroy()
        },
        methods: {
            showImagePrompt(command) {
                const src = prompt('Enter the url of your image here')
                if (src !== null) {
                    command({ src })
                }
            },
            showIframePrompt(command){
                const src = prompt('Enter the url of your iframe here')
                if (src !== null) {
                    command({ src })
                }
            },
            showLinkMenu(attrs) {
                this.linkUrl = attrs.href
                this.linkMenuIsActive = true
                this.$nextTick(() => {
                    this.$refs.linkInput.focus()
                })
            },
            hideLinkMenu() {
                this.linkUrl = null
                this.linkMenuIsActive = false
            },
            setLinkUrl(command, url) {
                command({href: url})
                this.hideLinkMenu()
                this.editor.focus()
            },
        }
    }
</script>
