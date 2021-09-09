export const HtmlUtil = {
    specialchars: function (str) {
        var s = [];
        if (!str) {
            return '';
        }
        if (str.length == 0) {
            return '';
        }
        for (var i = 0; i < str.length; i++) {
            switch (str.substr(i, 1)) {
                case "<":
                    s.push("&lt;");
                    break;
                case ">":
                    s.push("&gt;");
                    break;
                case "&":
                    s.push("&amp;");
                    break;
                case " ":
                    s.push("&nbsp;");
                    break;
                case "\"":
                    s.push("&quot;");
                    break;
                default:
                    s.push(str.substr(i, 1));
                    break;
            }
        }
        return s.join('');
    },
    text2html: function (str) {
        if (!str) {
            return '';
        }
        str = HtmlUtil.specialchars(str);
        str = str.replace(/\n/g, '</p><p>');
        return '<p>' + str + '</p>';
    },
}