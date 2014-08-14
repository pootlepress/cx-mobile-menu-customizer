(function ($) {

    $(window).load(function () {

        if (typeof MMM != 'undefined') {
//            console.log(MMM);
//            console.log(MMM.cartHtml);
//            console.log(MMM.shopIconClass);

            var html = MMM.cartHtml;

            $('#navigation .cart').remove();
            $('#navigation .side-nav').prepend(html);
//            $('#navigation .side-nav .cart .cart-contents').addClass(MMM.shopIconClass);

//            console.log('Cart Icon length: ' + $('#navigation .side-nav .cart i').length);
        }

    });

})(jQuery);