<input type="text" id="{{$param['name']}}Input" name="{{$param['name']}}" value='{{json_encode($memberUserIds)}}'/>

{!! \ModStart\ModStart::js('asset/vendor/tagify/jQuery.tagify.min.js') !!}
{!! \ModStart\ModStart::css('asset/vendor/tagify/tagify.css') !!}

<script>
    $(function () {
        var inputElm = document.querySelector("#{{$param['name']}}Input");

        var tagify = new Tagify(inputElm, {
            tagTextProp: 'name',
            enforceWhitelist: true,
            skipInvalid: true,
            dropdown: {
                closeOnSelect: false,
                enabled: 0,
                classname: 'tagify-dropdown-user-list',
                searchKeys: ['value', 'name']
            },
            templates: {
                tag: function (tagData) {
                    return `<tag title="${(tagData.name)}" contenteditable='false' spellcheck='false' tabIndex="-1"
                                class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}>
                            <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
                            <div>
                                <div class='tagify__tag__avatar-wrap'>
                                    <img class="avatar" onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
                                </div>
                                <span class='tagify__tag-text'>${tagData.name}</span>
                            </div>
                        </tag>`
                },
                dropdownItem: function (tagData) {
                    return `<div ${this.getAttributes(tagData)}
                            class='tagify__dropdown__item ${tagData.class ? tagData.class : ""}' tabindex="0" role="option">
                            ${tagData.avatar ? `
                            <div class='tagify__dropdown__item__avatar-wrap'>
                                <img class="avatar" onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
                            </div>` : ''}
                            <div class="name">${tagData.name}</div>
                        </div>`
                }
            },
            whitelist: {!! json_encode($memberUsers) !!},
            originalInputValueFormat: function (valuesArr) {
                var values = [];
                for (var i = 0; i < valuesArr.length; i++) {
                    values.push(valuesArr[i].value);
                }
                return JSON.stringify(values);
            }
        })
        var timer = null
        tagify.on('input', function (e) {
            var value = e.detail.value;
            console.log('input', value);
            tagify.settings.whitelist.length = 0;
            timer && clearTimeout(timer);
            tagify.loading(true).dropdown.hide.call(tagify)
            timer = setTimeout(function () {
                window.api.base.post("{{modstart_admin_url('member/search')}}", {keywords: value}, function (res) {
                    tagify.settings.whitelist = res.data
                    tagify.loading(false).dropdown.show.call(tagify, value);
                });
            }, 200);
        });
    });
</script>
