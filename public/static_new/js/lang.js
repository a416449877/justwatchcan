var language_pack = {
    loadProperties: function (new_lang) {
        var self = this;
        var tmp_lang = new_lang;
        self.now_lang = new_lang;
        jQuery.i18n.properties({
            name: 'strings',
            path: '/static_new/language/',
            language: tmp_lang,
            cache: false,
            mode: 'map',
            callback: function () {
                $("[data-lang]").each(function () {
                    if ($(this).attr('placeholder')) {
                        $(this).attr('placeholder', $.i18n.prop($(this).data('lang')));
                    } else {
                        $(this).html($.i18n.prop($(this).data("lang")));
                    }
                });
                for (var i in $.i18n.map) {
                    $('[data-lang="' + i + '"]').text($.i18n.map[i]);
                }
            }
        });
    }
}
$(document).ready(function () {
    var langtype = localStorage.getItem("langtype") || 'en'
    language_pack.loadProperties(langtype);
})