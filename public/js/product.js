$(document).ready(function () {
    $(".selectAllProductgGroups").on("click", function () {
        $("input[name='productGroups[]']").each(function (index) {
            $(this).prop('checked', true);

        });
    });
    
        $(".deSelectAllProductgGroups").on("click", function () {
        $("input[name='productGroups[]']").each(function (index) {
            $(this).prop('checked', false);

        });
    });

    $(".removeImage").on("click", function () {
        var productImageId = $(this).data('productimageid');
        var productId = $(this).data('productid');
        var ajaxURL = '/productAjax/deleteImage';
        $.ajax({
            type: 'POST',
            data: {
                productImageId: productImageId,
                productId: productId,
            },
            url: ajaxURL,
            async: true,
            success: function (data) {
                if (data.succes === true) {
                    $('div#productImage' + productImageId).fadeOut("slow", function () {
                        // Animation complete.
                    });
                } else {
                    alert('fout');
                }

            }
        });
    });

    $(".viewImage").on("click", function () {
        var productImageId = $(this).data('productimageid');
        var productId = $(this).data('productid');
        console.log(productImageId);
    });

    $(".changeImage").on("click", function () {
        var productImageId = $(this).data('productimageid');
        var productId = $(this).data('productid');
        console.log(productImageId);
    });

    $(".productFactTrigger").on("click", function () {
        var factid = $(this).data('factid');
        var elemnt = "productFactDescription" + factid;
        $(".productFactTriggerDescription").each(function (index) {
            if (elemnt != $(this).attr('id')) {
                $(this).slideUp("slow", function () {
                    // Animation complete.
                });
                $(this).siblings('div').children('span').removeClass('glyphicon-chevron-up');
                $(this).siblings('div').children('span').addClass('glyphicon-chevron-down');
            }
        });

        $("#productFactDescription" + factid).slideToggle("slow", function () {
            // Animation complete.
        });

        if ($(this).children('span').hasClass('glyphicon-chevron-down')) {
            $(this).children('span').removeClass('glyphicon-chevron-down');
            $(this).children('span').addClass('glyphicon-chevron-up');
        } else {
            $(this).children('span').removeClass('glyphicon-chevron-up');
            $(this).children('span').addClass('glyphicon-chevron-down');
        }

    });


});

$.getScript('https://code.jquery.com/ui/1.12.1/jquery-ui.js', function ()
{
    $(".grid").sortable({
        tolerance: 'pointer',
        revert: 'invalid',
        forceHelperSize: true,
        stop: function (event, ui) {
            var productImageId;
            var productImages = [];
            $("div#sortableItems div.span2").each(function (index) {
                productImageId = $(this).children('div').children('div').children('span.changeImage').data('productimageid');
                productImages.push(productImageId);
            });
            var ajaxURL = '/productAjax/sortImages';
            $.ajax({
                type: 'POST',
                data: {
                    productImages: productImages,
                },
                url: ajaxURL,
                async: true,
                success: function (data) {
                    if (data.succes === true) {
                        console.log('gesorteerd');
                    } else {
                        alert('fout');
                    }

                }
            });
        }
    });
});