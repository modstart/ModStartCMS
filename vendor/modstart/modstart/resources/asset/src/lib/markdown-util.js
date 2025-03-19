// const markdown = require("markdown").markdown
const showdown = require('showdown')

const converter = new showdown.Converter()

export const MarkdownUtil = {
    fixMarkdown: (md) => {
        let processed = md;

        // 补全未闭合代码块
        const codeBlockCount = (processed.match(/```/g) || []).length;
        if (codeBlockCount % 2 !== 0) {
            processed += '\n```';
        }

        // 处理表格未闭合
        const lines = processed.split('\n');
        let inTable = false;
        // 检查最后3行是否包含表格结构
        const checkTable = (lines) => {
            const tablePattern = /^(\|.*\|)\s*$/;
            const separatorPattern = /^(\|[-: ]+)+\|$/;
            return lines.some(line => tablePattern.test(line) || separatorPattern.test(line));
        };
        if (checkTable(lines.slice(-3))) {
            if (!/^\s*$/.test(lines[lines.length - 1])) {
                lines.push('');
            }
        }
        lines.forEach((line, index) => {
            if (line.startsWith('|') && !line.endsWith('|') && line.trim().length > 0) {
                lines[index] = line + '|';
            }
        });
        processed = lines.join('\n');

        // 补全列表项换行（需在真实转换前处理）
        const lastLine = lines[lines.length - 1];
        if (lastLine.match(/^(\s*[-*+]|\d+\.)\s/)) {
            processed += '\n';
        }

        return processed
    },
    toHtml: (md, autoFix) => {
        autoFix = autoFix || false
        if (autoFix) {
            md = MarkdownUtil.fixMarkdown(md)
        }
        try {
            return converter.makeHtml(md)
            // return markdown.toHTML(md)
        } catch (e) {
            return '解析错误' + e
        }
    }
}

