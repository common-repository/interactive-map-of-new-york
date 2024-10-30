( function( $ ) {

    "use strict";

    function isTouchEnabled() {
    return (('ontouchstart' in window)
        || (navigator.MaxTouchPoints > 0)
        || (navigator.msMaxTouchPoints > 0));
    }

    $(document).ready(function () {
        $("path[id^=\"nyusmapvorg_\"]").each(function (i, e) {
            addEvent($(e).attr('id'));
        });
    });

    function addEvent(id, relationId) {
        var _obj = $('#' + id);
        $('#nyusmapwrapper-org').css({'opacity': '1'});

        _obj.attr({'fill': nyus_map_org_config[id]['upclr'], 'stroke': nyus_map_org_config['default']['nyusbrdrclr_org']});

        _obj.attr({'cursor': 'default'});

        if (nyus_map_org_config[id]['enbl'] === true) {
            if (isTouchEnabled()) {
                var touchmoved;
                _obj.on('touchend', function (e) {
                    if (touchmoved !== true) {
                        _obj.on('touchstart', function (e) {
                            _obj.css({'fill': nyus_map_org_config[id]['dwnclr']});
                        })
                        _obj.on('touchend', function () {
                            _obj.css({'fill': nyus_map_org_config[id]['upclr']});
                            if (nyus_map_org_config[id]['targt'] === '_blank') {
                                window.open(nyus_map_org_config[id]['url']);
                            } else if (nyus_map_org_config[id]['targt'] === '_self') {
                                window.parent.location.href = nyus_map_org_config[id]['url'];
                            }
                        })
                    }
                }).on('touchmove', function (e) {
                    touchmoved = true;
                }).on('touchstart', function () {
                    touchmoved = false;
                });
            }
            _obj.attr({'cursor': 'pointer'});

            _obj.on('mouseenter', function () {
                _obj.css({'fill': nyus_map_org_config[id]['ovrclr']});
            }).on('mouseleave', function () {
                _obj.css({'fill': nyus_map_org_config[id]['upclr']});
            });
            if (nyus_map_org_config[id]['targt'] !== 'none') {
                _obj.on('mousedown', function () {
                    _obj.css({'fill': nyus_map_org_config[id]['dwnclr']});
                });
            }
            _obj.on('mouseup', function () {
                _obj.css({'fill': nyus_map_org_config[id]['ovrclr']});
                if (nyus_map_org_config[id]['targt'] === '_blank') {
                    window.open(nyus_map_org_config[id]['url']);
                } else if (nyus_map_org_config[id]['targt'] === '_self') {
                    window.parent.location.href = nyus_map_org_config[id]['url'];
                }
            });
        }
        else {
            _abb.css({'fill-opacity':'0.5'});
        }
    }
})(jQuery);
