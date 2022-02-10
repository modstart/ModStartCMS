$(function (){
    window.MS.util.loadStylesheet(window.__msCDN+'asset/vendor/prism/prism.css')
    window.MS.util.loadScript(window.__msCDN+'asset/vendor/prism/prism.js',function(){
        $('pre,code').each(function(i,o){
            var cls = $(o).attr('class');
            if(!cls){
                return;
            }
            var mat = cls.match(/brush:([a-z0-9]+)/);
            if(mat){
                $(o).addClass('language-'+mat[1]).html('<code>'+$(o).html()+'</code>');
            }
        });
        Prism.highlightAll();
    });
});
