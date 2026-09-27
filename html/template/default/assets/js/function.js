/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

$(function() {

    $('.pagetop').hide();

    $(window).on('scroll', function() {
        // ページトップフェードイン
        if ($(this).scrollTop() > 300) {
            $('.pagetop').fadeIn();
        } else {
            $('.pagetop').fadeOut();
        }

        // PC表示の時のみに適用
        if (window.innerWidth > 767) {

            if ($('.ec-orderRole').length) {

                var side = $(".ec-orderRole__summary"),
                    wrap = $(".ec-orderRole").first(),
                    min_move = wrap.offset().top,
                    max_move = wrap.height(),
                    margin_bottom = max_move - min_move;

                var scrollTop = $(window).scrollTop();
                if (scrollTop > min_move && scrollTop < max_move) {
                    var margin_top = scrollTop - min_move;
                    side.css({"margin-top": margin_top});
                } else if (scrollTop < min_move) {
                    side.css({"margin-top": 0});
                } else if (scrollTop > max_move) {
                    side.css({"margin-top": margin_bottom});
                }

            }
        }
        return false;
    });


    $('.ec-headerNavSP').on('click', function() {
        $('.ec-layoutRole').toggleClass('is_active');
        $('.ec-drawerRole').toggleClass('is_active');
        $('.ec-drawerRoleClose').toggleClass('is_active');
        $('body').toggleClass('have_curtain');
    });

    $('.ec-overlayRole').on('click', function() {
        $('body').removeClass('have_curtain');
        $('.ec-layoutRole').removeClass('is_active');
        $('.ec-drawerRole').removeClass('is_active');
        $('.ec-drawerRoleClose').removeClass('is_active');
    });

    $('.ec-drawerRoleClose').on('click', function() {
        $('body').removeClass('have_curtain');
        $('.ec-layoutRole').removeClass('is_active');
        $('.ec-drawerRole').removeClass('is_active');
        $('.ec-drawerRoleClose').removeClass('is_active');
    });

    // TODO: カート展開時のアイコン変更処理
    $('.ec-headerRole__cart').on('click', '.ec-cartNavi', function() {
        // $('.ec-cartNavi').toggleClass('is-active');
        $('.ec-cartNaviIsset').toggleClass('is-active');
        $('.ec-cartNaviNull').toggleClass('is-active')
    });

    $('.ec-headerRole__cart').on('click', '.ec-cartNavi--cancel', function() {
        // $('.ec-cartNavi').toggleClass('is-active');
        $('.ec-cartNaviIsset').toggleClass('is-active');
        $('.ec-cartNaviNull').toggleClass('is-active')
    });

    $('.ec-orderMail__link').on('click', function() {
        $(this).siblings('.ec-orderMail__body').slideToggle();
    });

    $('.ec-orderMail__close').on('click', function() {
        $(this).parent().slideToggle();
    });

    $('.is_inDrawer').each(function() {
        var html = $(this).html();
        $(html).appendTo('.ec-drawerRole');
    });

    $('.ec-blockTopBtn').on('click', function() {
        $('html,body').animate({'scrollTop': 0}, 500);
    });

    // スマホのドロワーメニュー内の下層カテゴリ表示
    // TODO FIXME スマホのカテゴリ表示方法
    $('.ec-itemNav ul a').click(function() {
        var child = $(this).siblings();
        if (child.length > 0) {
            if (child.is(':visible')) {
                return true;
            } else {
                child.slideToggle();
                return false;
            }
        }
    });

    // イベント実行時のオーバーレイ処理
    // classに「load-overlay」が記述されていると画面がオーバーレイされる
    $('.load-overlay').on({
        click: function() {
            loadingOverlay();
        },
        change: function() {
            loadingOverlay();
        }
    });

    // submit処理についてはオーバーレイ処理を行う
    $(document).on('click', 'input[type="submit"], button[type="submit"]', function() {

        // html5 validate対応
        var valid = true;
        var form = getAncestorOfTagType(this, 'FORM');

        if (typeof form !== 'undefined' && !form.hasAttribute('novalidate')) {
            // form validation
            if (typeof form.checkValidity === 'function') {
                valid = form.checkValidity();
            }
        }

        if (valid) {
            loadingOverlay();
        }
    });
});

$(window).on('pageshow', function() {
    loadingOverlay('hide');
});

/**
 * オーバーレイ処理を行う関数
 */
function loadingOverlay(action) {

    if (action == 'hide') {
        $('.bg-load-overlay').remove();
    } else {
        $overlay = $('<div class="bg-load-overlay">');
        $('body').append($overlay);
    }
}

/**
 *  要素FORMチェック
 */
function getAncestorOfTagType(elem, type) {

    while (elem.parentNode && elem.tagName !== type) {
        elem = elem.parentNode;
    }

    return (type === elem.tagName) ? elem : undefined;
}

// anchorをクリックした時にformを裏で作って指定のメソッドでリクエストを飛ばす
// Twigには以下のように埋め込む
// <a href="PATH" {{ csrf_token_for_anchor() }} data-method="(put/delete/postのうちいずれか)" data-confirm="xxxx" data-message="xxxx">
//
// ==========================================================================
// Modern Custom Alert & Confirm Modal System
// ==========================================================================
window.showEccubeConfirm = function(options) {
    options = options || {};
    var title = options.title || 'Are you sure?';
    var message = options.message || 'Do you want to continue?';
    var confirmText = options.confirmText || 'Confirm';
    var cancelText = options.cancelText || 'Cancel';
    var isDanger = (options.isDanger !== false);
    var iconType = options.iconType || (isDanger ? 'delete' : 'warning');
    var onConfirm = options.onConfirm || function() {};
    var onCancel = options.onCancel || function() {};

    // Remove any existing confirm dialogs
    $('.ec-confirm-modal-overlay').remove();

    var iconHtml = '<i class="fas fa-trash-alt"></i>';
    var iconClass = 'is-danger';
    if (iconType === 'favorite') {
        iconHtml = '<i class="fas fa-heart-broken"></i>';
        iconClass = 'is-favorite';
    } else if (iconType === 'cart-delete') {
        iconHtml = '<i class="fas fa-cart-arrow-down"></i>';
        iconClass = 'is-danger';
    } else if (iconType === 'warning') {
        iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
        iconClass = 'is-warning';
    } else if (iconType === 'info') {
        iconHtml = '<i class="fas fa-info-circle"></i>';
        iconClass = 'is-info';
    } else if (iconType === 'success') {
        iconHtml = '<i class="fas fa-check"></i>';
        iconClass = 'is-success';
    }

    var $overlay = $(
        '<div class="ec-confirm-modal-overlay" role="dialog" aria-modal="true" tabindex="-1">' +
        '  <div class="ec-confirm-modal-backdrop"></div>' +
        '  <div class="ec-confirm-modal-box">' +
        '    <button type="button" class="ec-confirm-modal-close-btn" aria-label="Close">' +
        '      <i class="fas fa-times"></i>' +
        '    </button>' +
        '    <div class="ec-confirm-modal-icon-badge ' + iconClass + '">' +
        '      ' + iconHtml +
        '    </div>' +
        '    <h3 class="ec-confirm-modal-title">' + title + '</h3>' +
        '    <p class="ec-confirm-modal-message">' + message + '</p>' +
        '    <div class="ec-confirm-modal-actions">' +
        '      <button type="button" class="ec-confirm-btn ec-confirm-btn--cancel">' + cancelText + '</button>' +
        '      <button type="button" class="ec-confirm-btn ' + (isDanger ? 'ec-confirm-btn--danger' : 'ec-confirm-btn--primary') + '">' + confirmText + '</button>' +
        '    </div>' +
        '  </div>' +
        '</div>'
    );

    $('body').append($overlay);
    $('body').addClass('ec-confirm-modal-open');

    // Smooth entrance
    setTimeout(function() {
        $overlay.addClass('is-active');
        $overlay.find('.' + (isDanger ? 'ec-confirm-btn--danger' : 'ec-confirm-btn--primary')).focus();
    }, 20);

    var closed = false;
    var closeModal = function(confirmed) {
        if (closed) return;
        closed = true;
        $overlay.removeClass('is-active');
        $('body').removeClass('ec-confirm-modal-open');
        $(document).off('keydown.ecConfirm');
        setTimeout(function() {
            $overlay.remove();
            if (confirmed) {
                onConfirm();
            } else {
                onCancel();
            }
        }, 260);
    };

    $overlay.find('.ec-confirm-btn--danger, .ec-confirm-btn--primary').on('click', function(e) {
        e.preventDefault();
        closeModal(true);
    });

    $overlay.find('.ec-confirm-btn--cancel, .ec-confirm-modal-close-btn, .ec-confirm-modal-backdrop').on('click', function(e) {
        e.preventDefault();
        closeModal(false);
    });

    $(document).on('keydown.ecConfirm', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            closeModal(false);
        }
    });
};

window.showEccubeAlert = function(options) {
    options = options || {};
    var title = options.title || 'Notice';
    var message = options.message || '';
    var btnText = options.btnText || 'Got it';
    var iconType = options.iconType || 'info';
    var onClose = options.onClose || function() {};

    $('.ec-confirm-modal-overlay').remove();

    var iconHtml = '<i class="fas fa-info-circle"></i>';
    var iconClass = 'is-info';
    if (iconType === 'success') {
        iconHtml = '<i class="fas fa-check"></i>';
        iconClass = 'is-success';
    } else if (iconType === 'warning') {
        iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
        iconClass = 'is-warning';
    } else if (iconType === 'error') {
        iconHtml = '<i class="fas fa-times-circle"></i>';
        iconClass = 'is-danger';
    }

    var $overlay = $(
        '<div class="ec-confirm-modal-overlay" role="dialog" aria-modal="true" tabindex="-1">' +
        '  <div class="ec-confirm-modal-backdrop"></div>' +
        '  <div class="ec-confirm-modal-box">' +
        '    <button type="button" class="ec-confirm-modal-close-btn" aria-label="Close">' +
        '      <i class="fas fa-times"></i>' +
        '    </button>' +
        '    <div class="ec-confirm-modal-icon-badge ' + iconClass + '">' +
        '      ' + iconHtml +
        '    </div>' +
        '    <h3 class="ec-confirm-modal-title">' + title + '</h3>' +
        '    <p class="ec-confirm-modal-message">' + message + '</p>' +
        '    <div class="ec-confirm-modal-actions">' +
        '      <button type="button" class="ec-confirm-btn ec-confirm-btn--primary" style="width: 100%;">' + btnText + '</button>' +
        '    </div>' +
        '  </div>' +
        '</div>'
    );

    $('body').append($overlay);
    $('body').addClass('ec-confirm-modal-open');

    setTimeout(function() {
        $overlay.addClass('is-active');
        $overlay.find('.ec-confirm-btn--primary').focus();
    }, 20);

    var closed = false;
    var closeModal = function() {
        if (closed) return;
        closed = true;
        $overlay.removeClass('is-active');
        $('body').removeClass('ec-confirm-modal-open');
        $(document).off('keydown.ecAlert');
        setTimeout(function() {
            $overlay.remove();
            onClose();
        }, 260);
    };

    $overlay.find('.ec-confirm-btn--primary, .ec-confirm-modal-close-btn, .ec-confirm-modal-backdrop').on('click', function(e) {
        e.preventDefault();
        closeModal();
    });

    $(document).on('keydown.ecAlert', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            closeModal();
        }
    });
};

// Global alert override to route any alert(...) calls to the redesigned modal
try {
    var _nativeAlert = window.alert;
    window.alert = function(msg) {
        if (window.showEccubeAlert) {
            window.showEccubeAlert({
                title: 'Notice',
                message: msg || '',
                iconType: 'info'
            });
        } else {
            _nativeAlert(msg);
        }
    };
} catch (e) {
    // Ignore if window.alert cannot be overwritten in certain environments
}

$(function() {
    var createForm = function(action, data) {
        var $form = $('<form action="' + action + '" method="post"></form>');
        for (input in data) {
            if (data.hasOwnProperty(input)) {
                $form.append('<input name="' + input + '" value="' + data[input] + '">');
            }
        }
        return $form;
    };

    $('a[token-for-anchor]').click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var data = $this.data();

        var submitAction = function() {
            loadingOverlay();
            var $form = createForm($this.attr('href'), {
                _token: $this.attr('token-for-anchor'),
                _method: data.method
            }).hide();

            $('body').append($form); // Firefox requires form to be on the page to allow submission
            $form.submit();
        };

        if (data.confirm != false) {
            var defaultMsg = (typeof eccube_lang !== 'undefined' && eccube_lang['common.delete_confirm']) 
                ? eccube_lang['common.delete_confirm'] 
                : 'Do you want to continue?';
            var msg = data.message ? data.message : defaultMsg;

            // Context-sensitive customization for title, icon, and buttons
            var isFavorite = $this.closest('.ec-favoriteRole').length > 0 || window.location.pathname.indexOf('favorite') !== -1;
            var isDelivery = $this.closest('.ec-addressList').length > 0 || window.location.pathname.indexOf('delivery') !== -1;
            var isDelete = (data.method || '').toLowerCase() === 'delete';

            var title = 'Confirm Action';
            var iconType = 'warning';
            var confirmBtnText = 'Confirm';

            if (isFavorite) {
                title = 'Remove from Favorites?';
                iconType = 'favorite';
                confirmBtnText = 'Yes, Remove';
                if (!data.message) {
                    // Try to grab item title if inside favorite list
                    var itemTitle = $this.closest('.ec-favoriteRole__item').find('.ec-favoriteRole__itemTitle').text().trim();
                    if (itemTitle) {
                        msg = 'Are you sure you want to remove "' + itemTitle + '" from your favorites?';
                    } else {
                        msg = 'Are you sure you want to remove this product from your favorites?';
                    }
                }
            } else if (isDelivery) {
                title = 'Delete Delivery Address?';
                iconType = 'delete';
                confirmBtnText = 'Delete Address';
                if (!data.message) {
                    msg = 'Are you sure you want to remove this delivery address from your account?';
                }
            } else if (isDelete) {
                title = 'Confirm Deletion';
                iconType = 'delete';
                confirmBtnText = 'Yes, Delete';
            }

            if (window.showEccubeConfirm) {
                window.showEccubeConfirm({
                    title: title,
                    message: msg,
                    iconType: iconType,
                    confirmText: confirmBtnText,
                    cancelText: 'Cancel',
                    isDanger: isDelete,
                    onConfirm: submitAction
                });
                return false;
            } else {
                if (!confirm(msg)) {
                    return false;
                }
            }
        }

        submitAction();
    });
});
