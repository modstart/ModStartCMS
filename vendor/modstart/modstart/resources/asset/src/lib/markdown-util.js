const markdown = require("markdown").markdown

export const MarkdownUtil = {
    toHtml: (html) => {
        try {
            return markdown.toHTML(html)
        } catch (e) {
            return '解析错误' + +e
        }
    }
}
