import {Node, Plugin} from 'tiptap'
import {nodeInputRule} from 'tiptap-commands'

/**
 * Matches following attributes in Markdown-typed iframe: [, alt, src, title]
 *
 * Example:
 * ![Lorem](iframe.jpg) -> [, "Lorem", "iframe.jpg"]
 * ![](iframe.jpg "Ipsum") -> [, "", "iframe.jpg", "Ipsum"]
 * ![Lorem](iframe.jpg "Ipsum") -> [, "Lorem", "iframe.jpg", "Ipsum"]
 */
const IMAGE_INPUT_REGEX = /!\[(.+|:?)\]\((\S+)(?:(?:\s+)["'](\S+)["'])?\)/

class Iframe extends Node {

    get name() {
        return 'iframe'
    }

    get schema() {
        return {
            inline: false,
            attrs: {
                src: {},
                title: {
                    default: null,
                },
            },
            group: 'block',
            draggable: true,
            parseDOM: [
                {
                    tag: 'iframe[src]',
                    getAttrs: dom => ({
                        src: dom.getAttribute('src'),
                        title: dom.getAttribute('title'),
                    }),
                },
            ],
            toDOM: node => ['iframe', node.attrs],
        }
    }

    commands({type}) {
        return attrs => (state, dispatch) => {
            const {selection} = state
            const position = selection.$cursor ? selection.$cursor.pos : selection.$to.pos
            const node = type.create(attrs)
            const transaction = state.tr.insert(position, node)
            dispatch(transaction)
        }
    }

    inputRules({type}) {
        return [
            nodeInputRule(IMAGE_INPUT_REGEX, type, match => {
                const [, src, title] = match
                return {
                    src,
                    title,
                }
            }),
        ]
    }

    get plugins() {
        return [
            new Plugin({
                props: {
                    handleDOMEvents: {
                        drop(view, event) {
                            const hasFiles = event.dataTransfer
                                && event.dataTransfer.files
                                && event.dataTransfer.files.length

                            if (!hasFiles) {
                                return
                            }

                            const iframes = Array
                                .from(event.dataTransfer.files)
                                .filter(file => (/iframe/i).test(file.type))

                            if (iframes.length === 0) {
                                return
                            }

                            event.preventDefault()

                            const {schema} = view.state
                            const coordinates = view.posAtCoords({left: event.clientX, top: event.clientY})

                            iframes.forEach(iframe => {
                                const reader = new FileReader()

                                reader.onload = readerEvent => {
                                    const node = schema.nodes.iframe.create({
                                        src: readerEvent.target.result,
                                    })
                                    const transaction = view.state.tr.insert(coordinates.pos, node)
                                    view.dispatch(transaction)
                                }
                                reader.readAsDataURL(iframe)
                            })
                        },
                    },
                },
            }),
        ]
    }

}

export {
    Iframe
}